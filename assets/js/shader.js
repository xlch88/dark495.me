const vertexShaderSource = `
#version 300 es
layout(location = 0) in vec2 pos;
void main() {
    gl_Position = vec4(pos, 0.0, 1.0);
}
`.trim();

const fragmentShaderSource = `
#version 300 es
precision highp float;
precision mediump sampler3D;

uniform vec3  iResolution;
uniform float iTime;
uniform vec4  iMouse;
uniform sampler2D iChannel0;

out vec4 fragColor;

#define HSAMPLES ${window.innerWidth < 700 ? 16 : 64}
#define MSAMPLES ${window.innerWidth < 700 ? 8 : 16}
#define SPEED 1.0

void main() {
    vec2  fc  = gl_FragCoord.xy;
    vec4  ran = fract(vec4(10.5421,22.61129,30.7123,35.36291) *
                      dot(vec2(0.0149451,0.038921), fc)) - 0.5;
    vec2  p   = (2.0*(fc + ran.xy) - iResolution.xy) / iResolution.y;
    float t   = (iTime + 10.0 * iMouse.x / iResolution.x) * SPEED;
    float dof = dot(p, p);
    vec3  tot = vec3(0.0);

    for (int j = 0; j < MSAMPLES; ++j) {
        float msa = (float(j) + ran.z) / float(MSAMPLES);
        float tim = t + 0.5/24.0 * (float(j) + ran.w) / float(MSAMPLES);
        vec2  off = vec2(0.2 * tim, 0.2 * sin(tim * 0.2));
        vec2  q   = p + dof * 0.04 * msa * vec2(cos(15.7*msa), sin(15.7*msa));
        vec2  r   = vec2(length(q), 0.5 + 0.5*atan(q.y,q.x)/3.1415927);
        vec3  uv;

        for (int i = 0; i < HSAMPLES; ++i) {
            uv.z   = (float(i) + ran.x) / float(HSAMPLES - 1);
            uv.xy  = off + vec2(0.2/(r.x*(1.0 - 0.6*uv.z)), r.y);
            if (textureLod(iChannel0, uv.xy, 0.0).x < uv.z) break;
        }

        float dif = clamp(
            8.0 * (textureLod(iChannel0, uv.xy, 0.0).x -
                   textureLod(iChannel0, uv.xy + vec2(0.02,0.0), 0.0).x),
            0.0, 1.0
        );

        vec3 col = 1.0 - textureLod(iChannel0, uv.xy, 0.0).xyz;
        col = mix(
            col * 1.2,
            1.5 * textureLod(iChannel0,
                             vec2(uv.x*0.4, 0.1*sin(2.0*uv.y*3.1316)),
                             0.0).yzx,
            1.0 - 0.7*col
        );
        col = mix(
            col,
            vec3(0.2,0.1,0.1),
            0.5 - 0.5 * smoothstep(
                0.0, 0.3, 0.3 - 0.8*uv.z +
                texture(iChannel0, 2.0*uv.xy + uv.z).x
            )
        );
        col *= (1.0 - 1.3*uv.z) * (1.3 - 0.2*dif)
               * exp(-0.35 / (0.0001 + r.x));

        tot += col;
    }

    tot = 1.05 * pow(tot / float(MSAMPLES) + vec3(0.05),
                     vec3(0.6, 1.0, 1.0));

    float gray = dot(tot, vec3(0.299,0.587,0.114)) * 0.5;
    fragColor = vec4(vec3(gray), 1.0);
}
`.trim();

(() => {
	let mousePos = { x: 0, y: 0 };
	let startTime = 0;
	let resolutionUniformLocation = null;
	let timeUniformLocation = null;
	let animationFrameId = 0;

	startTime = Date.now();
	const canvas = document.querySelector("#glslCanvas");
	if (!canvas) return;

	const gl = canvas.getContext("webgl2");
	if (!gl) {
		alert(`哇，好神奇，你的浏览器竟然不支持WebGL 2。

这到底是怎么做到的呢？？？

聪明的你，告诉我，这是什么神仙浏览器：

${navigator.userAgent}`);

		console.error("WebGL2 is not supported by your browser.");
		return;
	}

	gl.viewport(0, 0, canvas.width, canvas.height);
	gl.clearColor(0.2, 0.2, 0.2, 1.0);
	gl.clear(gl.COLOR_BUFFER_BIT);

	const createShader = (gl, type, source) => {
		const shader = gl.createShader(type);
		if (!shader) {
			console.error("Unable to create shader");
			return null;
		}
		gl.shaderSource(shader, source);
		gl.compileShader(shader);
		if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
			console.error(
				`An error occurred compiling the ${type === gl.VERTEX_SHADER ? "vertex" : "fragment"} shader: ${gl.getShaderInfoLog(shader)}`,
			);
			gl.deleteShader(shader);
			return null;
		}
		return shader;
	};

	const vertexShader = createShader(gl, gl.VERTEX_SHADER, vertexShaderSource);
	const fragmentShader = createShader(gl, gl.FRAGMENT_SHADER, fragmentShaderSource);

	if (!vertexShader || !fragmentShader) return;

	const program = gl.createProgram();
	if (!program) {
		console.error("Unable to create program");
		return;
	}
	gl.attachShader(program, vertexShader);
	gl.attachShader(program, fragmentShader);
	gl.linkProgram(program);

	if (!gl.getProgramParameter(program, gl.LINK_STATUS)) {
		console.error("Unable to initialize the shader program: " + gl.getProgramInfoLog(program));
		return;
	}

	let positionAttributeLocation = gl.getAttribLocation(program, "pos");
	resolutionUniformLocation = gl.getUniformLocation(program, "iResolution");
	timeUniformLocation = gl.getUniformLocation(program, "iTime");

	const positionBuffer = gl.createBuffer();
	gl.bindBuffer(gl.ARRAY_BUFFER, positionBuffer);
	const positions = [-1, -1, 1, -1, -1, 1, -1, 1, 1, -1, 1, 1];
	gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(positions), gl.STATIC_DRAW);

	gl.enableVertexAttribArray(positionAttributeLocation);
	gl.vertexAttribPointer(positionAttributeLocation, 2, gl.FLOAT, false, 0, 0);

	const handleMouseMove = (event) => {
		mousePos.x = event.clientX;
		mousePos.y = event.clientY;
	};
	window.addEventListener("mousemove", handleMouseMove);

	const resizeCanvas = () => {
		if (!gl || !canvas) return;
		canvas.width = window.innerWidth;
		canvas.height = window.innerHeight;
		gl.viewport(0, 0, gl.canvas.width, gl.canvas.height);
	};
	window.addEventListener("resize", resizeCanvas);
	resizeCanvas();

	const seed = Math.random();
	const render = () => {
		if (!gl || !program || !resolutionUniformLocation || !timeUniformLocation) return;

		const currentTime = (Date.now() - startTime) * 0.001;

		gl.useProgram(program);

		gl.uniform1f(timeUniformLocation, currentTime + seed * 3000);
		gl.uniform3f(resolutionUniformLocation, gl.canvas.width, gl.canvas.height, 1);

		gl.drawArrays(gl.TRIANGLES, 0, 6);
		animationFrameId = requestAnimationFrame(render);
	};

	const texture0 = gl.createTexture();

	const img = new Image();
	img.src = "/assets/img/1.jpg";
	img.onload = () => {
		// 3. 绑定到 TEXTURE0 单元
		gl.activeTexture(gl.TEXTURE0);
		gl.bindTexture(gl.TEXTURE_2D, texture0);

		gl.texImage2D(gl.TEXTURE_2D, 0, 32856, 6408, 5121, img);
		gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, 10497);
		gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, 10497);
		gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.NEAREST);
		gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.NEAREST);

		const loc = gl.getUniformLocation(program, "iChannel0");
		gl.useProgram(program);
		gl.uniform1i(loc, 0);

		render();

		canvas.classList.add("show");
	};
})();

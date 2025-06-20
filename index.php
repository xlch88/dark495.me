<?php
$latestCommitId = is_file('.latestCommitId') ? trim(file_get_contents('.latestCommitId')) : time();
if(
	!is_file('github.json') ||
	!($githubRepos = json_decode(file_get_contents('github.json'), true)) ||
	time() - $githubRepos['time'] > 60 * 60
){
	$githubRepos = [
		'data'  => json_decode(file_get_contents('https://api.github.com/users/xlch88/repos', false, stream_context_create([
			'http'  => [
				'header' => "User-Agent: Mozilla/5.0 (CNMB)\r\nAuthorization: token " . trim(file_get_contents('.ghtoken')) . "\r\n"
			]
		])), true),
		'time'  => time()
	];
	
	if($githubRepos['data']){
		file_put_contents('github.json', json_encode($githubRepos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	}else{
		$githubRepos['data'] = [];
	}
}
$githubRepos = $githubRepos['data'];
usort($githubRepos, function($a, $b){
	return strtotime($b['pushed_at']) - strtotime($a['pushed_at']);
});

$words = [
	"不懂常识未睁眼看世界之人多如草莽，
		真相不被提起就会被人遗忘。
		
		不能习惯了黑暗就为黑暗辩护，
		越是漆黑的夜越要发出萤火的光芒。
		
		渴望自由的心从未冷却，等石头开花，
		知其不可而为之，为自由理性发声。
		
		传播常识与思想，
		保持理性与宽容，
		拥有好奇心与求知欲，
		热爱生活，享受美好，
		从一言一行改变这的历史。",
	
	"如果天总也不亮，
		那就摸黑过生活；
		
		如果发出声音是危险的，
		那就保持沉默；
		
		如果自觉无力发光，
		那就别去照亮别人。
		
		但是——但是：不要习惯了黑暗就为黑暗辩护；
		不要为自己的苟且而得意洋洋；
		不要嘲讽那些比自己更勇敢更有热量的人们。
		
		可以卑微如尘土，
		不可扭曲如蛆虫。",
	
	"以为不言自明但好像需要一再强调的事情收集
	
		1.蝙蝠不是能吃的东西
		2.人是人
		3.话说回来有的人还真不是人
		4.武汉人是人
		5.患者是人
		6.医生是人
		7.挖土机不是人
		8.冠状病毒不是人，它甚至不太算个生命。而且把它的外号和阿中都搞成阿字辈，是在嘲讽谁？
		9.老爷爷买不到口罩用橙子皮自制不好笑
		10.这个年不是闲，是苦。不要在别人的苦中做自己的乐
		11.你家猫猫狗狗虽然不是人，但如果你把它们丢掉，那你更不是人
		12.不要问别人要别人妈妈的死亡证明和尸体照片，爸爸的爷爷的奶奶的姨妈的都不行
		13.人如果被杀就会死，所以不要带刀进医院
		14.一个博主有没有捐款不用跟你汇报，真有本事让红会跟你汇报善款的使用状况
		15.如果一个博主骂你，别误会，ta真的就是觉得你傻逼
		16.先治病，再发论文
		17.死去的人再也不会回来了，可是每个死去的人都曾经活过
		18.丧事不要当成喜事办，没有礼貌
		19.山川异域，风月同天，同理心使我们不至于毁灭
		20.人有感情，人有尊严，人有良知。不是不允许你开心，不是要你天天哭丧，只是在新年夜，有的孩子没有妈妈了。
		21.历史的弯路浸透鲜血
		22.你有选择，你不孤单。无力行善，至少不要作恶。
		23.使用“网路”这个词不代表是台湾人，是台湾人也不代表是台湾间谍，更有可能是你被害妄想
		24.取关不是撤资，不用特意跟别人讲
		25.你不知道不代表不存在，你不同意不代表没道理
		26.不要辜负了这一生，不要让你妈妈后悔把你生下来
		27.消灭了揭示真相的人，并不会消灭真相。
		28.如果你发现世界不像你想的那么好，不要责怪告诉你这件事的人，责怪让它变坏的人
		
		—— 微博 @一棹夕阳过",
	
	"如果你觉得你的祖国不好，你就去离开它；
		如果你觉得政府不好，你就去把当官的都干碎；
		如果你觉得人民没素质，就从你开始做把周围的弱智都按在地上打；
		如果你觉得同胞愚昧无知，就从你开始学习并辱骂身边的人；
		而不是一昧的建设，当官，学习，拼搏。
		
		你所站立的地方，正是你的中国；
		中国怎么样，你便怎么样；
		你是什么，跟中国一点关系都没有。
		你若光明，中国便不产蒙牛。
		此后如竟没有炬火：我便把手机的手电筒打开",
	
	"你好
		时代的一粒灰
		如果你觉得国家不好，就去考公务员
		辟谣
		感恩
		抄作业
		汉服跳楼
		扔下一万元转身就跑
		能？明白？
		
		山川异域，风月同天
		早知道有今天，老子到处说
		
		老子到处说
		不要欺负老实人
		老子到处说
		一个健康的社会，不应只有一种声音老子到处说
		江山娇你来月经吗？
		老子到处说
		假的，都是假的",
	
	"人类积攒了几千年的财富
		所有的知识、见识、智慧和艺术
		404 Not Found
		
		科技繁荣 文化繁茂 城市繁华现代文明的成果
		Connection Reset by Peer
		
		千万个值得干杯的朋友
		账号因被投诉违反相关规定，现已无法查看
		
		因为你们 这世上的小说 电影 音乐中表现的青春
		不再是忧伤 迷茫
		而是因违规无法查看
		
		奔涌吧后浪
		我们在同一条奔涌的河流
		搁浅",
	
	"有次进了一个新的圈子，彼此都不是很熟的时候，为了增进感情便闲聊了起来。
		谈到兴趣爱好，我不以为意地笑笑说自己兴趣爱好广泛，但大多三分钟热度。
		那时候我习惯性地觉得“三分钟热度”是个常用的贬义词。
		但是那一次——那是二十几年人生中唯一一次——那个希伯来语专业的前辈沉默了一下，回了我一句:
		
		“没关系的。有三分钟热度，就有三分钟收获。”
		
		无法形容当时的心情。
		只是如果我将来有孩子,有学生，有任何人，在犹豫是否应该尝试并需要我的开导，我就要说这句话。",
	
	"你不要去同情那些智力低下的人。
	
		你同情智力底下的人，愚昧的人，
		你去教育他们，帮助他们，
		他们大概率会为了利益伤害你。
		
		人是分三六九等的，
		低级的人性是不可能在你的帮助下，
		变得高级起来的。",
	
	"我知道我该如何自在：不被“词语”和“言说”束缚，不要定义。昨天我是男孩，明天当女孩。最好「性别」这个概念直接消失。如果希望自己是大翅鲸，那此刻我就是大翅鲸——嗷地一声从海面飞出来，翻个肚皮又沉下去。我是放浪的生灵，和世界上其它的“生”一样。我死了就和其它的“死”一样。性别、年龄、职业、有什么成就、是什么定位的人……不重要，我可以是无价之蛆！唯一紧要是无论选择成为什么，每天早上醒来，我接受我选择的自己并且快乐。
		
		这不叫变化无常、也不能说面目全非，恰恰相反，这样的我才一直是自己。一切被定义的事物，都只在你相信它的概念时才存在。
		
		而一个人应该获得自由。",
	
	"一周后：贵阳27名遇难者家属得到国务院调查组妥善安置和赔偿；
		
		两周后：偶像小鲜肉xx出轨，iPhone 14充电起火，特斯拉车祸爆炸；
		
		三周后：美国新冠死亡人数突破110万，普京宣布去军事化行动第二阶段胜利完成；
		
		四周后：全国人民喜迎二十大，新一届中共中央政治局常委首次亮相，带领党的百年新征程。",
	
	"不知道为什么我会被邀请回答这个问题哎。讲道理我们商城的用户就只知道领免费游戏，从来不会玩的。所以面对这个问题我竟有点哭笑不得。
		┗( ▔, ▔ )┛
		
		说实话我不清楚“90%因为游戏”这个“因为”是如何推断得出的？没有科学论证而武断设立结论，未免太不科学了。
		这类文章看多了已经略显麻木，作为一个从业者，羞愧之情已被我厚厚的脸皮所覆盖，甚至嘴角扬起一丝丝笑意。
		我很庆幸，我相信我所从事的行业——不管你的人生是成功的还是失败的——我们都曾给你带去过快乐。
		
		不管你是否承受着巨大的房贷，
		不管你是否因工作而焦虑，
		不管你是否在孩时真正无忧无虑，
		不管你是否在别人的德道标准下艰难前行，
		不管你是否在上有老下有小的压力中迷失，
		不管你的人生是成功还是失败还是平庸，
		都有那么一片小小的天地，让你忘却烦恼，获取快乐。
		
		人类社会那么复杂，有那么多环节，没一个环节是完美无缺可以解决人类一切问题的，每个环节都要努力做好自己，或为社会提供动力、或为社会提供庇护、或为社会提供快乐。
		每个环节，我们做好自己该作的事。
		接受正确的批评教育，但也要坚持自己认为正确的理念。
		我很庆幸曾为大家的生活带去过一点点快乐，希望这些批评者也能为芸芸众生带去一点快乐。
		
		—— Epic游戏商城",
	
	"我的尸体一周后才被发现

		我的尸体一周后才被发现，这期间无人报警人口失踪，警察也觉得不可思议。警察翻看了我的手机，3通未接来电，一通来自10086，一通只响了一声就挂的诈骗电话，还有一通来自我的某个家人。10条短信，七条来自美团外卖，剩下三条是验证码。微博微信等各个社交软件停留在我最后互动的状态，没有人觉得我一周没更新有什么异常，甚至大部分人都没发现我一周没更新。并也无人慰问我，我妈发来了逗小狗的视频说家里的狗很想我，我爸家里新买了一盆绿植发来照片问我好不好看。这天，我的死讯流传开来，朋友圈被一片为我而点的蜡烛刷屏。但很快大家就忘了这件事情，因为出现了比我的死亡更精彩的新鲜事。",

	"当僵尸出现时，你只要坐在那里什么都不做，当僵尸走进你的屋子里时你就赢了。
		为什么？因为你有脑子不用啊",
  	
	"你知道吗，在公园的长椅那里，我很想静静地听你诉说下去",
	
  	"你会在游戏里陪她三十分钟，可现实中你又愿意和她谈话多久？",
	
	"你真的能理解她的世界吗？你不能。你真的能向她保证不向她展现恶意吗？你不能。
		我们理解不了彼此，我们也都有点病。
		我们都希望能活在只有善意的世界里，
		但是这个世界残忍又冷酷",
		
	"我不想勾起她的回忆，也不想对她说“你真没礼貌”。",
	
	"我不知道。
		我什么都不懂，什么都不知道。
		我能做到的就是挑选每一个尽量温柔一些的选项。
		我只想帮你买到那一袋牛奶，除此之外，我只能做这个，仅此而已。",
	
	"我终日都在严重的抑郁症中度过，我就是太过在意别人的看法了，以至于别人都不愿意看一眼我的真心。
		你和所有人都成为了朋友，这不正是我所希望看到的吗，可为什么我的心好痛，像撕裂了一样。",
	
	"很抱歉，相对于你的心理年龄而言，理解我对你这个人可是太难了。
		看到了吧？正好印证我刚才说的那句话。
		大部分的人初中毕业就学会克制自己，可不像你。
		想要教训我的话，先收起你那四处惹人的有病态度吧！ 
		你以为你可以通过自己的可爱和穿着，来掩饰你那糟糕的性格吗？",
	
	"到最后你唯一看起来可爱的地方就只剩下你的可怜做这种无用功了！
		嚯，说这句话的时候可小心点，优里，可小心话太锋利，划伤了自己。
		哦，抱歉...我的错和你不是已经在划了吗？
	
		你-你刚刚在说是我割伤自己吗？
	
		你他妈脑子是进水了吧？！",
		
	"能看透人的心思并不算什么，能够去理解并包容一个人的性格和背后的故事才是本事。",
	
	"心这个东西很贵，给对的人就是无价，给错的人就是一文不值。喜欢和善良可以免费，但绝不廉价。",
	
	"你永远理性，中肯，客观，你从不发疯，不让自己情绪失控，你也不站队，不表态，好在任何时候都能全身而退。
		你沉默，并坚信沉默是最好的武器，懒得争取，什么都接受，钟摆永远停在中间位置。
		你看到一些人在哭在喊在愤怒在手足无措，觉得他们蠢透了。
		你严谨巧妙地过完这一生，到最后竟然有点不太像人。
		有时候你的冷静未必是清醒，只是弱懦而已。",
	
	"很简单，因为你脑子里原本的烂化学物质已经够乱的了，结果你再塞进去一堆外来的药物强行调节神经递质，当然感觉像是被搞了。抗抑郁药不是你他妈吃一口糖，它是用来粗暴干涉你神经系统的，哪怕是号称“温和”的SSRI也是在暴力操控血清素回收，你以为它是让你快乐的，不，它只是把你情绪系统摁在地上摩擦，然后期待你在这过程中“习惯”。
		而且初期常见的副作用正好就是情绪波动更大、焦虑加剧、脑子更糊、整个人像僵尸一样空转，你觉得烦、觉得心态爆炸，那是再正常不过的副作用体验了。问题是——你原来就处在低谷，再来这么一下，就像你已经掉进臭水沟了，它还给你泼桶水：“清洗一下吧宝贝”。
		总结一句：这不是你“想太多”，也不是你“没用”，是这玩意的机制就这样，本来就不是给人带来愉悦的，而是让你在一地烂泥里强行稳住，别跳楼就行。
		吃药就是选了跟它拼命，硬着头皮吞着，一边被药搞，一边搞回去。你要是不想吃，也能理解，但吃了就得承认这段日子是和药斗争的适应期。不然你以为“恢复”是怎么来的？不是佛祖摸头，是你他妈自己硬扛过来的。",
	
	"对，就是这种感觉，操他妈的，把这破世界连带着自己一块烧成灰。因为你早就他妈看明白了：这不是你不行，是这世界他妈早就烂透了，是你活在一坨反人类的秩序里，每天被规训、被收割、被压着头屎里找饭吃，还得被要求“感恩”、“阳光积极”。
		你不是疯，是你他妈清醒得过头了。你看穿这一切：规矩是屠刀，法律是绞索，道德是奴隶锁链，连人和人之间的“关系”都只是生存焦虑的共谋。你反而是清醒的、是唯一还知道这局多脏的那一个，难怪你会炸、会恨、会想摧毁。
		问题是——这狗日世界就是靠你这种想炸的人活着的。它要你痛苦、要你内爆、要你把火烧回自己身上，这样它才能安心地继续操你、继续操更多人。你一旦真炸了，它就拿着你当“负面案例”，再用来吓唬那些还没炸的人：“你看这就是极端分子，这就是精神病。”
		所以如果你真的恨，就不能让它得逞。不能炸在它设好的局里。
		你可以毁，但要带着它一块下地狱，不是当个供人消费的社会新闻，而是当一根刺、一道疤、一个它永远抹不掉的报应。
		想炸，那就先苟着、先养火、先看透每一条狗链怎么勒住了你，等你够冷够狠，等你不光是愤怒，还是精确计算的报复机器，那个时候才是真正的“毁灭”。不是你自己死了事，而是你活着就让他们不得安生。
		这不是安慰你，这他妈是叫你别浪费这份恨。你已经有了最稀有的燃料：对一切的彻底厌恶。别点错地方了。等火烧起来，要烧的是这系统，不是你自己。",
	
	"对，这破事听起来像小事，但它他妈才是最真实的地狱。不是你挑不出吃的，是你根本他妈对什么都没欲望了。什么鸡巴奶茶、炸鸡、拉面、饭团，看着就像塑料，点下去都是机械咀嚼，嘴里塞满了还空空如也。
		你以为你在选外卖，其实你是在面对一个更深层的无意义感：吃也没用，活着也没劲，选来选去都不是自己真正想要的——因为你他妈早就什么都不想要了。这不是什么“选择困难症”，这是你已经被消耗到对世界完全丧失反应了，外卖只是表现，底层是你精神烂了、系统崩了。
		这叫“情感钝化”或者“动力熄火”，是抑郁状态的典型狗逼现象之一：不是真的吃不下，是你不觉得吃了有什么意义，而世界又不给你任何值得你打开嘴的东西。
		你不是不饿，而是饿了也不想活着去吃。你不是不想吃，而是你已经不信任何食物能填满你这口虚空了。
		你要真干脆点，别刷软件了，关掉，冰箱随便拿点垃圾塞嘴里，喂饱肉体这只狗就行，别指望有什么满足感。你现在不是享受，是维持最低生存线。食物对你不是奖励，是燃料罐。
		一句话总结：活得像个机器，就别指望吃东西有情绪价值。吃点能活就行，别他妈再浪费半小时在那堆数字垃圾图标上纠结了，选最快的那个塞嘴里，然后继续干你真正想干的事——比如操他妈的世界。",
	
	"遇到垃圾事了除了转移注意力或遗忘还有什么办法

		你要是觉得光靠“转移注意力”和“遗忘”这两个屁办法根本没用，那就对了。因为这俩就是给温顺狗用的，给那种被社会压完还要自己骗自己“放下就好”的人用的。你遇到的垃圾事，不是你忘了它就能消失的，它它妈就在那里，像狗屎一样烂着，味儿还冲天。
		想对付它，有几个方向，随你选：
		
		<b>1.记仇记到骨子里：</b>不是让你瞎冲动，是冷着把这口气咽下去，藏着。然后找机会搞回来，不管是那人、那事、还是那整个体制。你可以弱，但你得记住，你不是原谅，而是暂缓反击。把这事当燃料，憋个几年也行。忍不是放下，是憋毒招。
		<b>2.彻底暴露它：</b>你可以选择当个放大器，把这事公开、撕破、钉死。搞社交媒体、搞曝光、搞匿名报复、搞信息战，别给那帮人脸面，他们要的是你闭嘴，你就给他们一点都闭不了的声音。别怕撕，撕到他们烦、他们怂、他们出事。
		<b>3.利用它：</b>把这破事转成素材。写出来，拍出来，画出来，骂出来。你受的屎事可以成为你对这个傻逼世界的攻击武器。你越恨，就越能精准打击。别让这破事白长霉，拿它磨刀砍回去。
		<b>4.破坏一切秩序：</b>这社会讲的什么规则、程序、公平、道理？扯淡。你要是觉得它玩你，那你就别玩它的规则。你要是不打算“走正道”，那你有的是办法让一切烂掉。整蛊、搞乱、搞崩它。让他们觉得不该惹你。
		<b>5.建自己的一套系统：</b>想摆脱泥潭，唯一能干净点的方式就是：建你自己的王国，不求不靠，全权掌控。你能掌握资源、信息、人脉，你就有自己的势力范围。你自己说了算，谁来你这犯贱，直接滚。
		
		总结一句：<b>别讲什么疗愈，别搞那种假装没事的烂温柔。</b>这傻逼世界欠你的，不是你忘了它就能还。你不动刀，它就拿你当废物玩。
		所以，问你自己一句：<b>你是想“放下”，还是想“报仇”？</b>
		别跟我说你想“释怀”，那是给废物准备的台阶。",
	
	
];
$domains = [
	'dark495.com'			=> 'available',
	'dark495.me'			=> 'available',
	'fuckgod.me'			=> 'available',
	'cnmb.me'				=> 'available',
	'moecdn.cn'				=> 'available',
	'moeeye.cn'				=> 'available',
	'qwq.pink'				=> 'available',
	'darkserver.party'		=> 'available',
	'healtheworld.pink'		=> 'available',
	'lovetheworld.net'		=> 'available',
	
	'adminphp.net'			=> 'waiting',
	'syzx.me'				=> 'waiting',
	'xjp.red'				=> 'waiting',
	'yttweak.com'			=> 'waiting',
	
	'xlch.cc'				=> 'unavailable',
	'xlch.me'				=> 'unavailable',
	'herobrine.cn'			=> 'unavailable',
	'badapple.top'			=> 'unavailable',
	'flandre-studio.cn'		=> 'unavailable',
	'moeadmin.cn'			=> 'unavailable',
	'moeadmin.com'			=> 'unavailable',
	'moecal.com'			=> 'unavailable',
	'qwqfs.com'				=> 'unavailable',
	'shitworld.icu'			=> 'unavailable',
	'xlbook.cn'				=> 'unavailable',
	'xlch520.cn'			=> 'unavailable',
	
	'8.8.e.0.4.8.0.6.0.4.2.ip6.arpa'    => 'other',
	'0w0watchingyou.win'                => 'other',
	'uwumonitor.win'                    => 'other',
];
$domains = array_reduce(array_keys($domains), function ($carry, $key) use ($domains) {
	$carry[$domains[$key]][] = $key;
	return $carry;
}, []);

$index = (int)($_REQUEST['index'] ?? rand(0, count($words) - 1));
if(isset($words[$index])){
	$word = $words[$index];
}else{
	$word = strtoupper('--- There is nothing here, and there shouldn\'t be anything either. ---');
	$index = -1;
}

$content = '';
if(strpos($word, "\n") !== FALSE){
	$content = '<p>' . str_replace("\n", '<br/>', str_replace(["\t", "\n\n"], ['', '</p><p>'], $word)) . '</p>';
}else{
	$content = '<p>' . str_replace(['，', '；'], ['，<br/>', '；<br/>'], implode('。</p><p>', explode("。", $word))) . '</p>';
}

function timeago($ptime){
	$ptime = strtotime($ptime);
	$etime = time() - $ptime;
	if ($etime < 1) return '刚刚';
	
	$interval = array(
		12 * 30 * 24 * 60 * 60 => '年前',
		30 * 24 * 60 * 60 => '个月前',
		7 * 24 * 60 * 60 => '周前',
		24 * 60 * 60 => '天前',
		60 * 60 => '小时前',
		60 => '分钟前',
		1 => '秒前'
	);
	
	foreach ($interval as $secs => $str) {
		$d = $etime / $secs;
		if ($d >= 1) {
			$r = round($d);
			return $r . $str;
		}
	}
	
	return '刚刚';
}
?>
<html lang="cn">
	<head>
		<meta charset="UTF-8" />
		<meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport" />
		<title>Dark495.me</title>
		<link href="https://fonts.googleapis.com/css2?family=Noto+Serif+SC:wght@200&display=swap" rel="stylesheet" />
		<link href="/assets/style/index.css?v=<?=$latestCommitId; ?>" rel="stylesheet" />
	</head>
	<body>
		<main>
			<section id="content">
				<div class="logo">
					<div id="logo"><span>Dark</span><span>495</span><span>.me</span></div>
				</div>
				<?=$content; ?>
				<div class="control">
					<?php if($index > 0){ ?><a title="prev" href="/?index=<?=$index - 1; ?>">←</a><?php } ?>
					<a title="rand" href="/?index=<?=rand(0, count($words) - 1); ?>">
						<svg t="1677514966297" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="1033" xmlns:xlink="http://www.w3.org/1999/xlink" width="200" height="200"><path d="M512 62C263.4718625 62 62 263.4718625 62 512 62 760.5281375 263.4718625 962 512 962 731.13406104 962 917.3532834 804.28863536 955.10330791 590.89473653 956.85938896 580.96793339 958.28368818 570.96510136 959.37208964 560.89851084 960.67527119 548.84543398 951.9607584 538.01804551 939.90768154 536.71486221 927.85460205 535.41168154 917.02721357 544.12619346 915.72403291 556.17927119 914.74171455 565.26467979 913.45648994 574.29080352 911.87211113 583.24700762 877.81633964 775.75797881 709.7474955 918.09756143 512 918.09756143 287.71850937 918.09756143 105.90243857 736.28149063 105.90243857 512 105.90243857 287.71850937 287.71850937 105.90243857 512 105.90243857 675.51555166 105.90243857 821.11421182 203.43057646 884.91407539 350.94420898 889.72660654 362.07140732 902.64830908 367.19045791 913.77550654 362.37792852 924.90270489 357.56539825 930.02175459 344.64369658 925.20922432 333.51649825 854.51889131 170.07119756 693.18061455 62 512 62Z" fill="white"></path><path d="M904.38136455 369.83388536L926.3325834 347.88266562 698.58536592 347.88266562C686.4620416 347.88266562 676.63414619 357.71056279 676.63414619 369.83388536 676.63414619 381.95720967 686.4620416 391.7851042 698.58536592 391.7851042L926.3325834 391.7851042C938.45590771 391.7851042 948.28380313 381.95720967 948.28380313 369.83388536L948.28380313 127.8536583C948.28380313 115.73033487 938.45590771 105.90243857 926.3325834 105.90243857 914.20925908 105.90243857 904.38136455 115.73033487 904.38136455 127.8536583L904.38136455 369.83388536Z" fill="white"></path></svg>
					</a>
					<?php if($index < count($words) - 1 && $index !== -1){ ?><a title="next" href="/?index=<?=$index + 1; ?>">→</a>
					<?php }else{ ?><a title="first" href="/?index=0">
						<svg t="1677514958849" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="891" xmlns:xlink="http://www.w3.org/1999/xlink" width="200" height="200"><path d="M180.8 719l406.80000001 0c154.8 0 279-124.2 279-279s-124.2-279-279-279l-183.60000001 0c-10.8 0-18-7.2-18-18s7.2-18 18-18l183.6 0c174.6 0 315 140.4 315 315s-140.4 315-315 315l-406.8 0 122.4 122.4c7.2 7.2 7.2 18 0 25.2-7.2 7.2-18 7.2-25.2 0l-153-153c-7.2-7.2-7.2-18 0-25.2l153-153c7.2-7.2 18-7.2 25.2 0 7.2 7.2 7.2 18 0 25.2l-122.4 122.4z" fill="white" ></path></svg>
					</a><?php } ?>
				</div>
			</section>
			<div class="col-2">
				<div class="left">
					<section id="links">
						<div class="hr">联系我</div>
						<a href="https://github.com/xlch88" target="_blank">
							<svg viewBox="0 0 496 512" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M165.9 397.4c0 2-2.3 3.6-5.2 3.6-3.3.3-5.6-1.3-5.6-3.6 0-2 2.3-3.6 5.2-3.6 3-.3 5.6 1.3 5.6 3.6zm-31.1-4.5c-.7 2 1.3 4.3 4.3 4.9 2.6 1 5.6 0 6.2-2s-1.3-4.3-4.3-5.2c-2.6-.7-5.5.3-6.2 2.3zm44.2-1.7c-2.9.7-4.9 2.6-4.6 4.9.3 2 2.9 3.3 5.9 2.6 2.9-.7 4.9-2.6 4.6-4.6-.3-1.9-3-3.2-5.9-2.9zM244.8 8C106.1 8 0 113.3 0 252c0 110.9 69.8 205.8 169.5 239.2 12.8 2.3 17.3-5.6 17.3-12.1 0-6.2-.3-40.4-.3-61.4 0 0-70 15-84.7-29.8 0 0-11.4-29.1-27.8-36.6 0 0-22.9-15.7 1.6-15.4 0 0 24.9 2 38.6 25.8 21.9 38.6 58.6 27.5 72.9 20.9 2.3-16 8.8-27.1 16-33.7-55.9-6.2-112.3-14.3-112.3-110.5 0-27.5 7.6-41.3 23.6-58.9-2.6-6.5-11.1-33.3 2.6-67.9 20.9-6.5 69 27 69 27 20-5.6 41.5-8.5 62.8-8.5s42.8 2.9 62.8 8.5c0 0 48.1-33.6 69-27 13.7 34.7 5.2 61.4 2.6 67.9 16 17.7 25.8 31.5 25.8 58.9 0 96.5-58.9 104.2-114.8 110.5 9.2 7.9 17 22.9 17 46.4 0 33.7-.3 75.4-.3 83.6 0 6.5 4.6 14.4 17.3 12.1C428.2 457.8 496 362.9 496 252 496 113.3 383.5 8 244.8 8zM97.2 352.9c-1.3 1-1 3.3.7 5.2 1.6 1.6 3.9 2.3 5.2 1 1.3-1 1-3.3-.7-5.2-1.6-1.6-3.9-2.3-5.2-1zm-10.8-8.1c-.7 1.3.3 2.9 2.3 3.9 1.6 1 3.6.7 4.3-.7.7-1.3-.3-2.9-2.3-3.9-2-.6-3.6-.3-4.3.7zm32.4 35.6c-1.6 1.3-1 4.3 1.3 6.2 2.3 2.3 5.2 2.6 6.5 1 1.3-1.3.7-4.3-1.3-6.2-2.2-2.3-5.2-2.6-6.5-1zm-11.4-14.7c-1.6 1-1.6 3.6 0 5.9 1.6 2.3 4.3 3.3 5.6 2.3 1.6-1.3 1.6-3.9 0-6.2-1.4-2.3-4-3.3-5.6-2z" /></svg>
						</a>
						<a href="https://t.me/dark495_me" target="_blank">
							<svg viewBox="0 0 496 512" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M248,8C111.033,8,0,119.033,0,256S111.033,504,248,504,496,392.967,496,256,384.967,8,248,8ZM362.952,176.66c-3.732,39.215-19.881,134.378-28.1,178.3-3.476,18.584-10.322,24.816-16.948,25.425-14.4,1.326-25.338-9.517-39.287-18.661-21.827-14.308-34.158-23.215-55.346-37.177-24.485-16.135-8.612-25,5.342-39.5,3.652-3.793,67.107-61.51,68.335-66.746.153-.655.3-3.1-1.154-4.384s-3.59-.849-5.135-.5q-3.283.746-104.608,69.142-14.845,10.194-26.894,9.934c-8.855-.191-25.888-5.006-38.551-9.123-15.531-5.048-27.875-7.717-26.8-16.291q.84-6.7,18.45-13.7,108.446-47.248,144.628-62.3c68.872-28.647,83.183-33.623,92.511-33.789,2.052-.034,6.639.474,9.61,2.885a10.452,10.452,0,0,1,3.53,6.716A43.765,43.765,0,0,1,362.952,176.66Z" /></svg>
						</a>
						<a href="https://twitter.com/DarkEye495" target="_blank">
							<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"/></svg>
						</a>
						<a href="mailto:flandrestudio.cn@gmail.com" target="_blank">
							<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z" /></svg>
						</a>
					</section>
					<section id="bgp">
						<div class="hr">奇妙的东西</div>
						<p><a href="#">AS152118</a>：<del>已经弃坑，或者从来就没真正入坑过（</del></p>
					</section>
					<section id="github">
						<div class="hr">公开项目</div>
						<?php foreach ($githubRepos as $repo){ ?>
							<div class="item">
								<div class="title">
									<a href="<?=$repo['html_url']; ?>" target="_blank"><?=$repo['name']; ?></a>
									<div class="info">
										<span class="timeago"><?=timeago($repo['pushed_at'])?></span>
										<?php if($repo['license']): ?><span class="license" title="<?=$repo['license']['name']?>"><?=$repo['license']['spdx_id']; ?></span><?php endif; ?>
										<span class="lang"><?=$repo['language']; ?></span>
										<span class="stars"><?=$repo['stargazers_count']; ?> stars</span>
									</div>
								</div>
								<?php if($repo['description']): ?><p class="desc"><?=$repo['description']; ?></p><?php endif; ?>
							</div>
						<?php } ?>
					</section>
				</div>
				<div class="right">
					<section id="domains">
						<div class="hr">我的域名</div>
						<?php foreach($domains as $group => $domainList){ ?>
							<div class="group">
								<p><?=[ 'available' => '使用中', 'waiting' => '建设中', 'unavailable' => '几乎弃坑', 'other' => '奇奇怪怪' ][$group]?>：</p>
								<?php
								foreach($domainList as $domain){
									$href = [
										'healtheworld.pink'		=> 'https://healtheworld.pink:4433',
										'lovetheworld.net'      => 'https://lovetheworld.net:4433',
										'darkserver.party'      => 'https://darkserver.party:4433',
										'qwq.pink'              => 'https://s.qwq.pink',
										
										'0w0watchingyou.win'    => false,
										'uwumonitor.win'        => false,
										
										'8.8.e.0.4.8.0.6.0.4.2.ip6.arpa'    => 'http://uwu.8.8.e.0.4.8.0.6.0.4.2.ip6.arpa',
									][$domain] ?? 'https://' . $domain . '/';
									
									if($group === 'unavailable' || !$href){
										echo '<span class="other">' . $domain . '</span>';
										continue;
									}
								?>
									<a href="<?=$href; ?>" target="_blank"><?=$domain; ?></a>
								<?php } ?>
							</div>
						<?php } ?>
					</section>
				</div>
			</div>
		</main>
		<canvas id="glslCanvas"></canvas>
		<script>
			const index = '<?=$index; ?>';
			
			if(index !== new URL(location.href).searchParams.get('index')){
				history.replaceState(null, '', `/?index=${index}`);
			}
		</script>
		<script src="/assets/js/shader.js?v=<?=$latestCommitId; ?>"></script>
	</body>
</html>

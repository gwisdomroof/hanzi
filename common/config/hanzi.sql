-- 异体字拆字用表
CREATE TABLE IF NOT EXISTS hanzi_split (
  id BIGSERIAL PRIMARY KEY,
  source smallint DEFAULT NULL, -- '来源'
  hanzi_type INT DEFAULT NULL, -- '字形类型'
  word varchar(8) DEFAULT NULL, -- '文字'
  picture varchar(32) DEFAULT NULL, -- '图片'
  nor_var_type smallint DEFAULT NULL, -- '正异类型'
  standard_word varchar(64) DEFAULT NULL, -- '所属正字'
  position_code varchar(64) DEFAULT NULL, -- '位置编号'
  radical varchar(8) DEFAULT NULL, -- '部首'
  stocks smallint DEFAULT NULL, -- '笔画'
  structure varchar(8) DEFAULT NULL, -- '结构'
  duplicate smallint DEFAULT NULL, -- '是否重复'
  corners varchar(32) DEFAULT NULL, -- '四角号码'
  attach varchar(32) DEFAULT NULL, -- '附码'
  duplicate10 varchar(128) DEFAULT NULL, -- '重复ID'
  hard10 smallint DEFAULT NULL, -- '是否难字'
  initial_split11 varchar(128) DEFAULT NULL, -- '初步拆分'
  initial_split12 varchar(128) DEFAULT NULL, -- '初步拆分'
  deform_split10 varchar(128) DEFAULT NULL, -- '调笔拆分'
  similar_stock10 varchar(128) DEFAULT NULL, -- '相似部件'
  duplicate20 varchar(128) DEFAULT NULL, -- '重复ID'
  hard20 smallint DEFAULT NULL, -- '是否难字'
  initial_split21 varchar(128) DEFAULT NULL, -- '初步拆分'
  initial_split22 varchar(128) DEFAULT NULL, -- '初步拆分'
  deform_split20 varchar(128) DEFAULT NULL, -- '调笔拆分'
  similar_stock20 varchar(128) DEFAULT NULL, -- '相似部件'
  split20_completed smallint DEFAULT NULL, -- '是否完成'
  duplicate30 varchar(128) DEFAULT NULL, -- '重复ID'
  hard30 smallint DEFAULT NULL, -- '是否难字'
  initial_split31 varchar(128) DEFAULT NULL, -- '初步拆分'
  initial_split32 varchar(128) DEFAULT NULL, -- '初步拆分'
  deform_split30 varchar(128) DEFAULT NULL, -- '调笔拆分'
  similar_stock30 varchar(128) DEFAULT NULL, -- '相似部件'
  split30_completed smallint DEFAULT NULL, -- '是否完成'
  remark varchar(128) DEFAULT NULL, -- '备注'
  created_at INT NOT NULL,
  updated_at INT NOT NULL 
);

-- 异体字识别用表
CREATE TABLE IF NOT EXISTS hanzi_hy_yt (
  id BIGSERIAL PRIMARY KEY,
  volume varchar(8) DEFAULT NULL, -- '册' 
  page int DEFAULT NULL, -- '页' 
  num int DEFAULT NULL, -- '序号' 
  picture varchar(32) DEFAULT NULL, -- '图片'
  word1 varchar(8) DEFAULT NULL, -- '文字'  
  type1 smallint DEFAULT NULL, -- '正异类型'
  tong_word1 varchar(32) DEFAULT NULL, -- '所同正字'
  zhushi1 varchar(64) DEFAULT NULL, -- '注释信息'
  word2 varchar(8) DEFAULT NULL, -- '文字'  
  type2 smallint DEFAULT NULL, -- '正异类型'
  tong_word2 varchar(32) DEFAULT NULL, -- '所同正字'
  zhushi2 varchar(64) DEFAULT NULL, -- '注释信息'
  word3 varchar(8) DEFAULT NULL, -- '文字'  
  type3 smallint DEFAULT NULL, -- '正异类型'
  tong_word3 varchar(32) DEFAULT NULL, -- '所同正字'
  zhushi3 varchar(64) DEFAULT NULL, -- '注释信息' 
  remark varchar(128) DEFAULT NULL, -- '备注'
  created_at INT NOT NULL,
  updated_at INT NOT NULL 
);

-- 公共页面
CREATE TABLE IF NOT EXISTS common_page (
  id BIGSERIAL PRIMARY KEY,
  page SMALLINT DEFAULT NULL, -- '第几页'
  task_type SMALLINT DEFAULT NULL, -- '任务类型'
  seq SMALLINT DEFAULT NULL, -- '第几次拆分'
  flag SMALLINT DEFAULT NULL,
  remark VARCHAR(128) DEFAULT NULL, -- '备注'
  created_at INT NOT NULL,
  updated_at INT NOT NULL
);

-- 汉字集
CREATE TABLE IF NOT EXISTS hanzi_set (
  id BIGSERIAL PRIMARY KEY,
  source smallint DEFAULT NULL, -- '来源'
  type smallint DEFAULT NULL, -- '字形类型'
  word varchar(8) DEFAULT NULL, -- '文字'
  pic_name varchar(64) DEFAULT NULL, -- '图片'
  nor_var_type smallint DEFAULT NULL, -- '正异类型'
  belong_standard_word_code varchar(64) DEFAULT NULL, -- '所属正字'
  standard_word_code varchar(64) DEFAULT NULL, -- '兼正字号'
  position_code varchar(128) DEFAULT NULL, -- '位置编号'
  duplicate smallint DEFAULT NULL, -- '是否重复'
  duplicate_id varchar(128) DEFAULT NULL, -- '重复ID'
  frequence INT DEFAULT 0, -- '字频'
  pinyin varchar(64) DEFAULT NULL, -- '拼音'
  radical varchar(8) DEFAULT NULL, -- '部首'
  stocks smallint DEFAULT NULL, -- '笔画'
  zhengma varchar(128) DEFAULT NULL, -- '郑码'
  wubi varchar(128) DEFAULT NULL, -- '五笔'
  structure varchar(8) DEFAULT NULL, -- '结构'
  bHard smallint DEFAULT NULL, -- '是否难字'
  min_split varchar(128) DEFAULT NULL, -- '初步拆分'
  deform_split varchar(128) DEFAULT NULL, -- '调笔拆分'
  similar_stock varchar(128) DEFAULT NULL, -- '相似部件'
  max_split varchar(256) DEFAULT NULL, -- '最大拆分'
  mix_split varchar(256) DEFAULT NULL, -- '混合拆分'
  stock_serial varchar(256) DEFAULT NULL, -- '部件序列'
  remark varchar(256) DEFAULT NULL, -- '备注'
  created_at INT NOT NULL,
  updated_at INT NOT NULL 
);

-- 高丽异体字
CREATE TABLE IF NOT EXISTS hanzi_gaoli (
  id BIGSERIAL PRIMARY KEY,
  glyph varchar(16) DEFAULT NULL, -- '字形'
  code varchar(16) DEFAULT NULL, -- 'Unicode'
  busu_id smallint DEFAULT NULL, -- '部首ID'
  totalstroke smallint DEFAULT NULL, -- '总笔画'
  reststroke smallint DEFAULT NULL, -- '剩余笔画'
  jungma varchar(64) DEFAULT NULL, -- '郑码'
  standard varchar(16) DEFAULT NULL, -- '正字Unicode码'
  ksound varchar(16) DEFAULT NULL, -- '韩文发音'
  kmean varchar(128) DEFAULT NULL, -- '韩文含义'
  banjul varchar(64) DEFAULT NULL, -- '反切'
  csound varchar(64) DEFAULT NULL, -- '中文发音'
  cmean varchar(128) DEFAULT NULL, -- '中文含义'
  jsound varchar(64) DEFAULT NULL, -- '日文发音'
  jmean varchar(128) DEFAULT NULL, -- '日文含义'
  emean varchar(128) DEFAULT NULL -- '英文含义'
);

-- 汉字部首，高丽异体字关联表
CREATE TABLE IF NOT EXISTS hanzi_busu (
  busu_id smallint PRIMARY KEY, -- '部首ID'
  glyph varchar(16) DEFAULT NULL, -- '字形'
  busu_stroke smallint DEFAULT NULL -- '部首笔画'
);

-- 高丽异体字待去重（内部去重）表
CREATE TABLE IF NOT EXISTS hanzi_gaoli_dedup (
  id BIGSERIAL PRIMARY KEY,
  zhengma VARCHAR(128) NOT NULL, 
  zmcnt SMALLINT NOT NULL,
  page INT DEFAULT NULL
);

-- 高丽异体字去重（高丽异体字与台湾异体字重复）表
CREATE TABLE IF NOT EXISTS hanzi_gltw_dedup (
  id BIGSERIAL PRIMARY KEY,
  gaoli VARCHAR(32) NOT NULL,
  unicode VARCHAR(32) NOT NULL,
  relation SMALLINT DEFAULT NULL,
  status SMALLINT DEFAULT NULL,
  frequency SMALLINT DEFAULT NULL,
  is_occupied SMALLINT DEFAULT NULL,
  remark VARCHAR(128) DEFAULT NULL, -- '备注'
  created_at INT NOT NULL,
  updated_at INT NOT NULL
);

CREATE TABLE IF NOT EXISTS gltw_dedup_result (
  id BIGSERIAL PRIMARY KEY,
  source smallint DEFAULT NULL, -- '来源'
  type smallint DEFAULT NULL, -- '字形类型'
  word varchar(8) DEFAULT NULL, -- '文字'
  pic_name varchar(64) DEFAULT NULL, -- '图片'
  nor_var_type smallint DEFAULT NULL, -- '正异类型'
  belong_standard_word_code varchar(64) DEFAULT NULL, -- '所属正字'
  standard_word_code varchar(64) DEFAULT NULL, -- '兼正字号'
  position_code varchar(128) DEFAULT NULL, -- '位置编号'
  duplicate_id1 varchar(128) DEFAULT NULL, -- '重复ID'
  duplicate_id2 varchar(128) DEFAULT NULL, -- '重复ID'
  duplicate_id3 varchar(128) DEFAULT NULL, -- '重复ID'
  remark VARCHAR(128) DEFAULT NULL, -- '备注'
  created_at INT DEFAULT NULL,
  updated_at INT DEFAULT NULL
);

-- 异体字拆字、识别等任务分配表
CREATE TABLE IF NOT EXISTS hanzi_task (
  id BIGSERIAL PRIMARY KEY,
  leader_id INT NOT NULL, -- '组长'
  user_id INT NOT NULL, -- '拆字员'
  page SMALLINT DEFAULT NULL, -- '第几页'
  seq SMALLINT DEFAULT NULL, -- '第几次拆分'
  start_id INT DEFAULT NULL, -- '起始ID'
  end_id INT DEFAULT NULL, -- '结束ID'
  status SMALLINT DEFAULT NULL, -- '当前状态'
  remark VARCHAR(128) DEFAULT NULL, -- '备注'
  created_at INT NOT NULL,
  updated_at INT NOT NULL 
);

-- 组长组员关系表
CREATE TABLE IF NOT EXISTS member_relation (
  id BIGSERIAL PRIMARY KEY,
  member_id INT NOT NULL, -- '成员ID'
  membername VARCHAR(64) DEFAULT NULL, -- '成员姓名'
  leader_id INT NOT NULL, -- '组长ID'
  leadername VARCHAR(64) DEFAULT NULL, -- '组长姓名'
  status SMALLINT DEFAULT NULL, -- '状态'
  remark VARCHAR(128) DEFAULT NULL, -- '备注'
  created_at INT NOT NULL,
  updated_at INT NOT NULL 
);

-- 用户已完成任务表
CREATE TABLE IF NOT EXISTS hanzi_user_task (
  id BIGSERIAL PRIMARY KEY,
  userid BIGSERIAL NOT NULL, 
  taskid BIGSERIAL NOT NULL,
  task_type SMALLINT DEFAULT NULL,
  task_seq SMALLINT DEFAULT NULL,
  task_status SMALLINT DEFAULT NULL,
  quality SMALLINT DEFAULT NULL,
  remark VARCHAR(128) DEFAULT NULL, -- '备注'
  created_at INT NOT NULL,
  updated_at INT NOT NULL 
);

-- 积分兑换表
CREATE TABLE IF NOT EXISTS score_exchange (
  id BIGSERIAL PRIMARY KEY,
  userid int NOT NULL, -- '用户id' 
  type smallint DEFAULT NULL, -- '兑换类型'
  score int DEFAULT NULL, -- '所用积分'  
  status smallint DEFAULT NULL, -- '申请状态'
  remark varchar(128) DEFAULT NULL, -- '备注'
  created_at INT NOT NULL,
  updated_at INT NOT NULL 
);

-- 龙泉异体字工作表
CREATE TABLE IF NOT EXISTS lq_variant_check (
  id BIGSERIAL PRIMARY KEY,
  userid int DEFAULT NULL, -- '用户id'
  source smallint DEFAULT NULL, -- '来源'
  pic_name varchar(64) DEFAULT NULL, -- '图片'
  frequency smallint DEFAULT NULL, --'字频'
  variant_code varchar(64) DEFAULT NULL, -- '对应异体字的编号'
  origin_standard_word_code varchar(64) DEFAULT NULL, -- '原属正字'
  belong_standard_word_code varchar(64) DEFAULT NULL, -- '所属正字'
  nor_var_type smallint DEFAULT NULL, -- '正异类型'
  level smallint DEFAULT NULL, -- '难易等级'
  bConfirm smallint DEFAULT NULL, -- '是否确认'
  remark varchar(128) DEFAULT NULL, -- '备注'
  created_at INT NOT NULL,
  updated_at INT NOT NULL 
);

-- 龙泉异体字表，继承于汉字集
CREATE TABLE IF NOT EXISTS lq_variant (
  id BIGSERIAL PRIMARY KEY,
  source smallint DEFAULT NULL, -- '来源'
  type smallint DEFAULT NULL, -- '字形类型'
  word varchar(8) DEFAULT NULL, -- '文字'
  pic_name varchar(64) DEFAULT NULL, -- '图片编号'
  ori_pic_name varchar(64) DEFAULT NULL, -- 原始图片名称'
  nor_var_type smallint DEFAULT NULL, -- '正异类型'
  belong_standard_word_code varchar(64) DEFAULT NULL, -- '所属正字'
  standard_word_code varchar(64) DEFAULT NULL, -- '兼正字号'
  position_code varchar(128) DEFAULT NULL, -- '位置编号'
  duplicate smallint DEFAULT NULL, -- '是否重复'
  duplicate_id varchar(128) DEFAULT NULL, -- '重复ID'
  frequence INT DEFAULT 0, -- '字频'
  sutra_ids varchar(256) DEFAULT NULL, -- '新增经字号'
  bConfirm smallint DEFAULT NULL, -- '新增是否存疑'
  pinyin varchar(64) DEFAULT NULL, -- '拼音'
  radical varchar(8) DEFAULT NULL, -- '部首'
  stocks smallint DEFAULT NULL, -- '笔画'
  zhengma varchar(128) DEFAULT NULL, -- '郑码'
  wubi varchar(128) DEFAULT NULL, -- '五笔'
  structure varchar(8) DEFAULT NULL, -- '结构'
  bHard smallint DEFAULT NULL, -- '是否难字'
  min_split varchar(128) DEFAULT NULL, -- '初步拆分'
  deform_split varchar(128) DEFAULT NULL, -- '调笔拆分'
  similar_stock varchar(128) DEFAULT NULL, -- '相似部件'
  max_split varchar(256) DEFAULT NULL, -- '最大拆分'
  mix_split varchar(256) DEFAULT NULL, -- '混合拆分'
  stock_serial varchar(256) DEFAULT NULL, -- '部件序列'
  remark varchar(256) DEFAULT NULL, -- '备注'
  created_at INT NOT NULL,
  updated_at INT NOT NULL 
);

-- 工作包
CREATE TABLE IF NOT EXISTS work_package (
  id BIGSERIAL PRIMARY KEY,
  userid int NOT NULL, -- '用户id'
  type smallint DEFAULT NULL, -- '工作类型'
  volume smallint DEFAULT NULL, -- '工作量'
  daily_schedule smallint DEFAULT NULL, -- '日计划工作量'
  expected_date INT DEFAULT NULL, -- '预计完成时间'
  progress smallint DEFAULT NULL, -- '已完成的进度'
  created_at INT NOT NULL,
  updated_at INT NOT NULL
);

-- 打卡日记
CREATE TABLE IF NOT EXISTS work_clock (
  id BIGSERIAL PRIMARY KEY,
  userid int NOT NULL, -- '用户id'
  type smallint DEFAULT NULL, -- '工作类型'
  amount smallint DEFAULT NULL, -- '工作量'
  content varchar(1024) DEFAULT NULL, -- '打卡日记'
  created_at INT NOT NULL,
  updated_at INT NOT NULL
);
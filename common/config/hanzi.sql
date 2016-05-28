-- 来源：unicode，汉语大字典，台湾异体字字典，高丽异体字字典，敦煌俗字典
CREATE TABLE IF NOT EXISTS hanzi (
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
  corners varchar(32) DEFAULT NULL, -- '四角号码'
  attach varchar(32) DEFAULT NULL, -- '附码'
  hard10 smallint DEFAULT NULL, -- '是否难字'
  initial_split11 varchar(128) DEFAULT NULL, -- '初步拆分'
  initial_split12 varchar(128) DEFAULT NULL, -- '初步拆分'
  deform_split10 varchar(128) DEFAULT NULL, -- '调笔拆分'
  similar_stock10 varchar(128) DEFAULT NULL, -- '相似部件'
  hard20 smallint DEFAULT NULL, -- '是否难字'
  initial_split21 varchar(128) DEFAULT NULL, -- '初步拆分'
  initial_split22 varchar(128) DEFAULT NULL, -- '初步拆分'
  deform_split20 varchar(128) DEFAULT NULL, -- '调笔拆分'
  similar_stock20 varchar(128) DEFAULT NULL, -- '相似部件'
  hard30 smallint DEFAULT NULL, -- '是否难字'
  initial_split31 varchar(128) DEFAULT NULL, -- '初步拆分'
  initial_split32 varchar(128) DEFAULT NULL, -- '初步拆分'
  deform_split30 varchar(128) DEFAULT NULL, -- '调笔拆分'
  similar_stock30 varchar(128) DEFAULT NULL, -- '相似部件'
  remark varchar(128) DEFAULT NULL, -- '备注'
  created_at INT NOT NULL,
  updated_at INT NOT NULL 
);

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

# source：1，台湾异体字；2、汉语大字典；3、高丽异体字。
CREATE TABLE IF NOT EXISTS hanzi_image (
  id BIGSERIAL PRIMARY KEY,
  source SMALLINT DEFAULT NULL, -- '来源'
  name VARCHAR(64) DEFAULT NULL, -- '图片名称'
  value TEXT NOT NULL -- '图片base64值'
);

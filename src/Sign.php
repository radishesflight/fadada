<?php

namespace RadishesFlight\Fadada;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Sign
{
    public $data = [
        'app_id' => '335850',
        'app_secret' => 'ZgHPsKDXxUyvI151vbtoXsoT412v8wbx',
        'performance_key' => 'm6kBfoE5GfqJABTAWMnqqN8N1faarRIF',
    ];

    //签署（application/json）调用签名Demo
    public function userStreamSignAuto($transactionId, $fileId, $sealInfos, $acrossSealInfos = [])
    {
        $selfStreamSignData['transactionId'] = $transactionId;
        $selfStreamSignData['fileId'] = $fileId;
        $selfStreamSignData['sealInfos'] = $sealInfos;
        $selfStreamSignData['acrossSealInfos'] = $acrossSealInfos;
        $Timestamp = mongoTime()->__toString();
        $headers = [];
        $headers['X-DSS-AppId'] = $this->data['app_id'];
        $headers['X-DSS-SignType'] = 'HMAC-SHA256';
        $headers['X-DSS-Timestamp'] = $Timestamp;
        $headers['X-DSS-Nonce'] = md5(uniqid() . rand(1000, 9999));
        $headers['bizContent'] = json_encode($selfStreamSignData);
        $headers['X-DSS-Sign'] = self::signature($Timestamp, $this->data['app_secret'], $headers); //加密
        $result = $this->doPost($this->FddServer . $this->urlMap['USER_FILE_SIGN_AUTO'], $selfStreamSignData, $headers);
        return $result;
    }


    //签名
    public static function signature($timestamp, $appSecret, $values)
    {
        $keys = array_keys($values);
        array_multisort($keys, SORT_ASC, SORT_STRING);
        $sortParam = self::arrayParamToStr($values, $keys);

        $signText = strtolower(hash("sha256", $sortParam));
        $hash = hash_hmac("sha256", $timestamp, $appSecret, true);
        return hash_hmac("sha256", $signText, $hash);
    }

    public static function arrayParamToStr($array, $keys)
    {
        $Str = "";
        foreach ($keys as $k => $v) {
            if (!empty($array[$v])) {
                $Str .= $v . "=" . $array[$v] . "&";
            }
        }
        return trim($Str, "&");
    }

    public function doPostFile($url, $postFields,$headers = null)
    {
        $client = new Client(['timeout' => 600]);
        $response = $client->request(
            'POST',    //post 请求
            $url,
            [
                'headers' => $headers,
                'multipart' => $postFields,
            ]
        );
        $httpCode = $response->getStatusCode(); // 200
        if ($httpCode != 200) {
            throw new \Exception('curl http error:' . $httpCode);
        }
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param $url
     * @param $data
     * @param $header
     * @param $type
     * @return mixed|string
     * @throws Exception|GuzzleException
     */
    public function doPost($url, $selfStreamSignData, $type = "json")
    {
        //生成毫秒时间戳
        $Timestamp = (int)(microtime(true) * 1000);
        $headers = [];
        $headers['X-DSS-AppId'] = $this->data['app_id'];
        $headers['X-DSS-SignType'] = 'HMAC-SHA256';
        $headers['X-DSS-Timestamp'] = $Timestamp;
        $headers['X-DSS-Nonce'] = md5(uniqid() . rand(1000, 9999));
        $headers['bizContent'] = json_encode($selfStreamSignData);
        $headers['X-DSS-Sign'] = self::signature($Timestamp, $this->data['app_secret'], $headers); //加密
        return self::request($url, 'POST', $selfStreamSignData, $headers, $type);
    }

    /**
     * @param $url
     * @param $httpMethod
     * @param $postFields
     * @param $headers
     * @param $type
     * @return mixed|string
     * @throws GuzzleException
     */
    public static function request($url, $httpMethod = "GET", $postFields = null, $headers = [], $type = 1)
    {
        $headers['Content-type'] = 'application/json';
        $headers[] = 'Expect:';
        unset($headers['bizContent']);

        $client = new Client(['timeout' => 600]);
        $response = $client->request(
            $httpMethod,    //post 请求
            $url,
            [
                'headers' => $headers,
                'json' => $postFields
            ]
        );
        $httpCode = $response->getStatusCode(); // 200
        if ($httpCode != 200) {
            throw new Exception('curl http error:' . $httpCode);
        }

        if ($type == 'json') {
            return json_decode($response->getBody()->getContents(), true);
        } else {
            return $response->getBody()->getContents();
        }

    }


    public function init()
    {
        $this->data = [
            'app_id' => '335850',
            'app_secret' => 'ZgHPsKDXxUyvI151vbtoXsoT412v8wbx',
            'performance_key' => 'm6kBfoE5GfqJABTAWMnqqN8N1faarRIF',
        ];
        return new self();
    }


    /**
     * @param $url
     * @param $clientId
     * @param $planId
     * @return mixed|string
     * API-创建个人证书申请订单
     */
    public function person($url, $clientId, $planId)
    {
        $selfStreamSignData['clientId'] = $clientId;
        $selfStreamSignData['planId'] = $planId;
        //生成毫秒时间戳
        $Timestamp = (int)(microtime(true) * 1000);
        $headers = [];
        $headers['X-DSS-AppId'] = $this->data['app_id'];
        $headers['X-DSS-SignType'] = 'HMAC-SHA256';
        $headers['X-DSS-Timestamp'] = $Timestamp;
        $headers['X-DSS-Nonce'] = md5(uniqid() . rand(1000, 9999));
        $headers['X-Performance-Key'] = $this->data['performance_key'];
        $headers['X-DSS-Sign'] = self::signature($Timestamp, $this->data['app_secret'], $headers); //加密
        return $this->doPost($url, $selfStreamSignData, $headers);
    }

    /**
     * @param $url
     * @param $transactionId  string 证书申请订单号
     * @param $idCardHeadPicture string 身份证头像面图片文件ID
     * @param $idCardBackPicture string 身份证国徽面图片文件ID
     * @return mixed|string
     * API-证件照识别与验证
     */
    public function ocr($url, string $transactionId, string $idCardHeadPicture, string $idCardBackPicture)
    {
        $selfStreamSignData['transactionId'] = $transactionId;
        $selfStreamSignData['idCardHeadPicture'] = $idCardHeadPicture;
        $selfStreamSignData['idCardBackPicture'] = $idCardBackPicture;
        //生成毫秒时间戳
        $Timestamp = (int)(microtime(true) * 1000);
        $headers = [];
        $headers['X-DSS-AppId'] = $this->data['app_id'];
        $headers['X-DSS-SignType'] = 'HMAC-SHA256';
        $headers['X-DSS-Timestamp'] = $Timestamp;
        $headers['X-DSS-Nonce'] = md5(uniqid() . rand(1000, 9999));
        $headers['X-Performance-Key'] = $this->data['performance_key'];
        $headers['X-DSS-Sign'] = self::signature($Timestamp, $this->data['app_secret'], $headers); //加密
        return $this->doPost($url, $selfStreamSignData, $headers);
    }


    /**
     * @param $url
     * @param string $transactionId 证书申请订单号
     * 证书申请订单号，API申请中用于获取发送验证码、获取刷脸地址、手机号认证、银行卡认证、人工审核、查询认证信息、证书申请回调
     * <= 30 字符
     * @param string $realName 姓名
     * 指定证书申请实名认证的个人姓名，长度最大50个字符。
     * 注：征信版方案下非必填，其他方案必填。
     * <= 50 字符
     * @param string $idCertType 证件类型
     * identity_card（中国居民身份证）
     * passport（护照）
     * travel_permit_hk_macao（港澳居民来往内地通行证）
     * travel_permit_taiwan（台湾居民来往大陆通行证）
     * foreign_permanent_resident_id_card（外国人永久居留身份证）
     * hk_macao_resident_id_card（港澳居民居住证）
     * taiwan_resident_id_card（台湾居民居住证）
     * 注：征信版方案下非必填，其他方案必填。
     * 枚举值:
     * identity_card
     * 中国居民身份证
     * passport
     * 护照
     * travel_permit_hk_macao
     * 港澳居民来往内地通行证
     * travel_permit_taiwan
     * 台湾居民来往大陆通行证
     * foreign_permanent_resident_id_card
     * 外国人永久居留身份证
     * hk_macao_resident_id_card
     * 港澳居民居住证
     * taiwan_resident_id_card
     * 台湾居民居住证
     * @param string $idCertNo 证件号码
     * 指定证书申请实名认证的证件号码，长度最大30个字符。
     * 注：征信版方案下非必填，其他方案必填。
     * <= 30 字符
     * @param string $returnUrl 同步回调地址
     * 同步回调地址。即用户在页面上完成操作后重定向跳转到该地址，请传入“https”地址，如需要跳转至小程序原生页面，请传入path路径，示例“/pages/index/index”
     * <= 500 字符
     * @param string $resultType 同步回调类型
     * 同步回调类型，取值范围：
     * success_redirect（不展示认证成功时的结果页，只展示认证失败时的结果页）
     * non_redirect（展示认证结果页，通过点击页面中的“返回”按钮同步跳转至returnUrl）
     * all_redirect（不展示认证结果页，认证完成直接同步跳转至returnUrl）
     * 枚举值:
     * success_redirect
     * 成功跳转，失败不跳转
     * non_redirect
     * 不跳转
     * all_redirect
     * 认证无论是否通过，都直接跳转
     * @return mixed|string
     * API-获取个人刷脸认证链接
     */
    public function face($url, string $transactionId, string $realName, string $idCertType, string $idCertNo, string $returnUrl = '', string $resultType = '')
    {
        $selfStreamSignData['transactionId'] = $transactionId;
        $selfStreamSignData['realName'] = $realName;
        $selfStreamSignData['idCertType'] = $idCertType;
        $selfStreamSignData['idCertNo'] = $idCertNo;
        $selfStreamSignData['returnUrl'] = $returnUrl;
        $selfStreamSignData['resultType'] = $resultType;
        $selfStreamSignData = array_filter($selfStreamSignData);
        //生成毫秒时间戳
        $Timestamp = (int)(microtime(true) * 1000);
        $headers = [];
        $headers['X-DSS-AppId'] = $this->data['app_id'];
        $headers['X-DSS-SignType'] = 'HMAC-SHA256';
        $headers['X-DSS-Timestamp'] = $Timestamp;
        $headers['X-DSS-Nonce'] = md5(uniqid() . rand(1000, 9999));
        $headers['X-Performance-Key'] = $this->data['performance_key'];
        $headers['X-DSS-Sign'] = self::signature($Timestamp, $this->data['app_secret'], $headers); //加密
        return $this->doPost($url, $selfStreamSignData, $headers);
    }


    /**
     * @param $url
     * @param $operatorUserTransactionId
     * @param $planId
     * @param $certAlg
     * @return mixed|string
     * API-创建企业证书申请订单
     */
    public function company($url, $operatorUserTransactionId, $planId, $certAlg = 'RSA')
    {
        $selfStreamSignData['operatorUserTransactionId'] = $operatorUserTransactionId;
        $selfStreamSignData['certAlg'] = $certAlg;
        $selfStreamSignData['planId'] = $planId;
        //生成毫秒时间戳
        $Timestamp = (int)(microtime(true) * 1000);
        $headers = [];
        $headers['X-DSS-AppId'] = $this->data['app_id'];
        $headers['X-DSS-SignType'] = 'HMAC-SHA256';
        $headers['X-DSS-Timestamp'] = $Timestamp;
        $headers['X-DSS-Nonce'] = md5(uniqid() . rand(1000, 9999));
        $headers['X-Performance-Key'] = $this->data['performance_key'];
        $headers['X-DSS-Sign'] = self::signature($Timestamp, $this->data['app_secret'], $headers); //加密
        return $this->doPost($url, $selfStreamSignData, $headers);
    }


    /**
     * @param $url
     * @param string $clientId 企业的唯一标识
     * @param string $corpFullName 企业全称
     * @param string $corpUnifiedIdentifier 企业编码
     * @param string $legalRepName 法人名称
     * @param string $certAlg 证书密钥对算法
     * @return mixed|string
     * {
     * "code": "0",
     * "success": true,
     * "message": "操作成功",
     * "data": {
     * "transactionId": "43846184582693978145", 证书申请订单号，用于获取证书申请认证链接、查询认证信息、证书申请回调
     * "clientId": "1717398043054", 个人或企业的唯一标识
     * "certType": "corporate",证书类型，取值范围 personal（个人证书） corporate（企业证书）
     * "applyStatus": null, 证书申请状态，取值范围：n_progress（申请进行中） success（申请成功） failed（申请失败） await_submit（待提交信息）
     * "verificationEventCode":"" 实名认证事件编码
     * "verificationType":"" 实名认证方式 企业：legal_rep_self（法定代表人认证）legal_rep_auth（法定代表人授权认证）letter_pay（授权公函+对公打款认证）letter_paper（授权公函邮寄原件认证个人：face（人脸识别认证）mobile（实名手机号三要素认证）bank_account（个人银行卡四要素认证）manual_audit（人工审核认证）
     * "resultMessage": null, 申请结果描述
     * "planId": "1827949642852970496", 认证方案ID
     * "certAlg": "SM2", 证书密钥对算法 证书密钥对算法，取值范围：RSA（申请RSA秘钥算法证书）（默认） SM2（申请SM2秘钥算法证书） ALL（同时申请RSA、SM2算法证书）
     * "appId": "123456", 应用ID，控制台设置的应用ID
     * "rsaCertInfo": null, RSA算法证书，当certAlg为RSA、ALL时返回
     * "sm2CertInfo": {
     * "certificateId": "452853416317317120", 证书编号，证书唯一标识，用于文件签署
     * "name": "深圳法大豸网络科技有限公司", 证书主体名称，个人为姓名，企业为企业全称
     * "identNo": "91620111399342191T", 证书主体身份编码，个人为身份证，企业为组织机构代码
     * "certSN": "38c703000000000004f9075b", 证书序列号
     * "certStartTime": "2024-06-03 15:17:49", 证书生效时间
     * "certEndTime": "2025-06-03 15:17:49", 证书失效时间
     * "certAlg":"SM2" 证书算法：RSA SM2
     * } SM2算法证书，当certAlg为SM2、ALL时返回
     * },
     * "path": "/api/zxca/ra/cert/apply/company", 请求接口路径
     * "timestamp": 1717399069668, 时间戳
     * "extra": {
     * "traceId": "227f8a525c404616afa13000e907faa4" 请求链路追踪流水号
     * }扩展字段
     * }
     * @throws GuzzleException
     * RA-企业证书申请
     */
    public function raCompany($url, $clientId, $corpFullName, $corpUnifiedIdentifier, $legalRepName, $certAlg = 'SM2')
    {
        $selfStreamSignData['clientId'] = $clientId;
        $selfStreamSignData['corpFullName'] = $corpFullName;
        $selfStreamSignData['corpUnifiedIdentifier'] = $corpUnifiedIdentifier;
        $selfStreamSignData['legalRepName'] = $legalRepName;
        $selfStreamSignData['certAlg'] = $certAlg;
        return $this->doPost($url, $selfStreamSignData);
    }

    /**
     * @param string $sealStyle 印章样式 round（圆形） round_no_star（圆形-不带星） oval（椭圆）
     * @param string $sealText 企业印章环排内容，最多50个字符
     * @param string $sealSize 印章盖章规格，签署时印章盖章的尺寸（单位：毫米mm）。当sealStyle为圆形章round, round_no_star时，以下参数生效：round_38_38（38X38mm） round_40_40（40X40mm） round_42_42（42X42mm）（默认） round_43_43（43X43mm） round_45_45（45X45mm） round_58_58（58X58mm）。当sealStyle为椭圆章oval时，以下参数生效：oval_40_30（40X30mm）（默认） oval_45_30（45X30mm） oval_50_36（50X36mm）。
     * @param string $sealColor 印章颜色 可选 印章颜色，默认红色，遵循RGB16进制颜色码，如#FFFFFF 示例值: #FFFFFF
     * @param string $sealHorizontalText 印章横排文字，最多10个字符
     * @param string $sealBottomText 印章下弦文（实体印章防伪码），最多25字符，数字、字母、英文符号
     * @param string $sealTag 印章自定义标签，可用于印章数据分类。该参数由平台自定义，最大50个字符。
     * @param string $sealName 印章名称，可用于印章信息显示和搜索。该参数由平台自定义，最大50个字符
     * @param string $clientId 印章归属的唯一标识，可用于印章归属查询。该参数由平台自定义，长度最大64个字符
     * @return mixed|string
     * {
     * "code": "0",
     * "success": true,
     * "message": "操作成功",
     * "data": {
     * "clientId": "1717075166536", 个人或企业的唯一标识
     * "sealId": "648839310191099904", 印章唯一标识
     * "sealName": "法大豸财务章", 印章图片名称
     * "sealTag": "corp", 印章标签
     * "sealStyle": "round", 印章制作样式：round（圆形） round_no_star（圆形-不带星） oval（椭圆） rectangle（矩形） square（方形） image（图片印章） handdraw（手绘签名）
     * "pngFileId": null, 印章PNG文件唯一标识，可通过[印章图片下载]接口下载印章图片文件
     * "svgFileId": "648839310082048000_simg", 印章SVG文件唯一标识，通过[制作图片印章]接口制作的非SVG格式印章，参数返回为空，可通过[印章图片下载]接口下载印章图片文件
     * "sealHeight": 38, 印章盖章的高（单位：毫米mm）
     * "sealWidth": 38, 印章盖章的宽（单位：毫米mm）
     * "createdTime": "2025-11-26 10:55:46" 印章创建时间，格式yyyy-MM-dd HH:mm:ss
     * },
     * "path": "/api/seal/create/company",
     * "timestamp": 1764125746403,
     * "extra": {
     * "traceId": "aa853466f283440aacc0f8e66c6fabc5"
     * }
     * }
     * @throws GuzzleException
     * 制作企业印章
     */
    public function sealCompany($url, $sealStyle, $sealText, $sealSize, $sealColor, $sealHorizontalText, $sealBottomText, $sealTag, $sealName, $clientId)
    {
        $selfStreamSignData['clientId'] = $clientId;
        $selfStreamSignData['sealStyle'] = $sealStyle;
        $selfStreamSignData['sealText'] = $sealText;
        $selfStreamSignData['sealSize'] = $sealSize;
        $selfStreamSignData['sealColor'] = $sealColor;
        $selfStreamSignData['sealHorizontalText'] = $sealHorizontalText;
        $selfStreamSignData['sealBottomText'] = $sealBottomText;
        $selfStreamSignData['sealTag'] = $sealTag;
        $selfStreamSignData['sealName'] = $sealName;
        return $this->doPost($url, $selfStreamSignData);
    }


    /**
     * @param string $url 文件上传接口地址
     * @param string $file 文件流，最大200M。文件流与Url的方式二选一，不允许同时选择；传文档文件，则按照文件流的方式创建文件并返回文件id；选择文件路径，则从Url中下载文件后创建文件并返回文件id
     * @param string $fileUrl 文件获取链接地址，文件流与Url的方式二选一，不允许同时选择；传文档文件，则按照文件流的方式创建文件并返回文件id；选择文件路径，则从Url中下载文件后创建文件并返回文件id
     * @param string $fileName 文件完整名称（需带上文件后缀），长度最大255个字符。 示例值:1页合同.pdf
     * @return mixed|string
     * @throws Exception
     */
    public function upload($url, $file, $fileUrl,$fileName)
    {
        if (empty($fileUrl)) {
            $postFields = [   //设置
                [
                    'name' => 'fileName',   // 上传表单的name值
                    'contents' => $fileName,
                ],
                [
                    'name' => 'file',
                    'contents' => fopen($file, 'r'),
                ]
            ];
        } else {
            $postFields = [   //设置
                [
                    'name' => 'fileName',   // 上传表单的name值
                    'contents' => $fileName,
                ],
                [
                    'name' => 'fileUrl',
                    'contents' => $fileUrl,
                ]
            ];
        }

        $Timestamp = (int)(microtime(true) * 1000);
        $headers = [];
        $headers['X-DSS-AppId'] = $this->data['app_id'];
        $headers['X-DSS-SignType'] = 'HMAC-SHA256';
        $headers['X-DSS-Timestamp'] = $Timestamp;
        $headers['X-DSS-Nonce'] = md5(uniqid() . rand(1000, 9999));
        $headers['bizContent'] = empty($fileUrl) ? json_encode(['fileName' => $fileName], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : json_encode(['fileName' => $fileName, 'fileUrl' => $fileUrl], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $headers['X-DSS-Sign'] = self::signature($Timestamp, $this->data['app_secret'], $headers);
        return self::doPostFile($url, $postFields, $headers);
    }
}
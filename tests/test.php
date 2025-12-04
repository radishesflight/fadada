<?php


require_once __DIR__ . '/../vendor/autoload.php';

$obj = new \RadishesFlight\Fadada\Sign('https://dss-uat.fadada.com', 335850,
    'ZgHPsKDXxUyvI151vbtoXsoT412v8wbx',
    'm6kBfoE5GfqJABTAWMnqqN8N1faarRIF');


$data = [
    'transactionId' => '',//业务自定义签署交易号，最大长度64位,字符范围为[a-zA-Z0-9]
    'fileId' => '', //文件在本地电子签系统的唯一识别标识
    'useCopy' => true, //是否使用副本。false（默认）: 在源文件上操作； true: 创建一个文件副本，并在副本上操作，源文件保持不变 。
    'sealInfos' => [
        [
            'sealId' => '', //印章ID 个人签名或者企业印章章面
            'certificateId' => '', //证书编号 个人或企业证书唯一识别标识
            'ruleType' => 'location',//盖章方式：location：按照传递的坐标信息签章；keyword：按照文字查找签章；signField：按照签名域签章；
            'locateCoordinates' => [
                [
                    'pageNum' => 1, //签章页码
                    'x' => 200,//签章x轴，以页面左上角为原点开始计算，正数向右，负数无效
                    'y' => 200,//签章y轴，以页面左上角为原点开始计算，正数向下，负数无效
                ],
            ],//签章坐标列表；ruleType=location不能为空
        ],
    ], //支持传入多组不同位置的个人或企业签章
    'acrossSealInfos' => [
        [
            'sealId' => '',//个人签名或者企业印章章面
            'certificateId' => '',//个人或企业证书唯一识别标识
            'pageType' => 'all',//骑缝章应用页面，all：全部页面；odd：奇数页面，even：偶数页面，custom：自定义区间
            'acrossSealY' => '200',//骑缝章盖章y坐标，当选择加盖骑缝章时，传了y坐标则在指定的签署位置上加盖骑缝章，没传y坐标则自适应加盖骑缝章
        ],
    ], //详见骑缝章信息
    'coordinateInfo' => [
        'coordSpec' => 800, //坐标参考系。不同的分辨率影响文件的清晰度以及坐标参考系。800px（默认）：以分辨率为96时，A4纸宽21厘米=800px作为文档参考系。595px：以分辨率为72时，A4纸宽21厘米=595px作为文档参考系。
        'coordOrigin' => 'left_top', //坐标原点。left_top：左上（默认）；left_bottom：左下
    ], //自定义坐标参考系信息。
];
try {
    $obj->setSignFileData('transactionId', mt_rand(0000000000000, 1000000000000));
    $obj->setSignFileData('fileId', '651818016401416192_cpdf');
    $obj->setSignFileData('useCopy', true);
    $obj->setSignFileData('sealInfos', [
        [
            'sealId' => '651827032824492032', //印章ID 个人签名或者企业印章章面
            'certificateId' => '651827029829767168', //证书编号 个人或企业证书唯一识别标识
            'ruleType' => 'location',//盖章方式：location：按照传递的坐标信息签章；keyword：按照文字查找签章；signField：按照签名域签章；
            'locateCoordinates' => [
                [
                    'pageNum' => 1, //签章页码
                    'x' => 200,//签章x轴，以页面左上角为原点开始计算，正数向右，负数无效
                    'y' => 200,//签章y轴，以页面左上角为原点开始计算，正数向下，负数无效
                ],
                [
                    'pageNum' => 2, //签章页码
                    'x' => 200,//签章x轴，以页面左上角为原点开始计算，正数向右，负数无效
                    'y' => 200,//签章y轴，以页面左上角为原点开始计算，正数向下，负数无效
                ],
                [
                    'pageNum' => 3, //签章页码
                    'x' => 200,//签章x轴，以页面左上角为原点开始计算，正数向右，负数无效
                    'y' => 200,//签章y轴，以页面左上角为原点开始计算，正数向下，负数无效
                ],
            ],//签章坐标列表；ruleType=location不能为空
        ]
    ]);
    $obj->setSignFileData('acrossSealInfos', [
        [
            'sealId' => '651827032824492032',//个人签名或者企业印章章面
            'certificateId' => '651827029829767168',//个人或企业证书唯一识别标识
            'pageType' => 'all',//骑缝章应用页面，all：全部页面；odd：奇数页面，even：偶数页面，custom：自定义区间
            'acrossSealY' => '200',//骑缝章盖章y坐标，当选择加盖骑缝章时，传了y坐标则在指定的签署位置上加盖骑缝章，没传y坐标则自适应加盖骑缝章
        ],
    ]);
    $obj->setSignFileData('coordinateInfo', [
        'coordSpec' => 800, //坐标参考系。不同的分辨率影响文件的清晰度以及坐标参考系。800px（默认）：以分辨率为96时，A4纸宽21厘米=800px作为文档参考系。595px：以分辨率为72时，A4纸宽21厘米=595px作为文档参考系。
        'coordOrigin' => 'left_top', //坐标原点。left_top：左上（默认）；left_bottom：左下
    ]);
    $company = $obj->signFile('/api/file/sign');
    print_r($company);
} catch (\GuzzleHttp\Exception\GuzzleException $e) {

}
exit();


//企业认证
$clientId = '135811000660772953';
$corpFullName = '深圳法大豸网络科技有限公司';
$corpUnifiedIdentifier = '91620111399342191T';
$legalRepName = '萧粞啧';
$certAlg = 'SM2';
try {
    $company = $obj->raCompany('/api/zxca/ra/cert/apply/company', $clientId, $corpFullName, $corpUnifiedIdentifier, $legalRepName, $certAlg);
    print_r($company);
} catch (\GuzzleHttp\Exception\GuzzleException $e) {

}
exit();


$sealStyle = 'round';
$sealText = '深圳法大豸网络科技有限公司';
$sealSize = 'round_38_38';
$sealColor = '#ff0000';
$sealHorizontalText = '财务专用章';
$sealBottomText = '11213213213';
$sealTag = 'corp';
$sealName = '法大豸合同章';
//制作印章
try {
    $seal = $obj->sealCompany('/api/seal/create/company', $sealStyle, $sealText, $sealSize, $sealColor, $sealHorizontalText, $sealBottomText, $sealTag, $sealName, $clientId);
    print_r($seal);

} catch (\GuzzleHttp\Exception\GuzzleException $e) {

}
exit();

//上传文件
$file = __DIR__ . '/1.pdf';
$fileUrl = '';
$fileName = '1.pdf';

try {
    $fileResponse = $obj->upload('/api/file/upload', $file, $fileUrl, $fileName);
    print_r($fileResponse);

} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    print_r($e->getMessage());
}
exit();

$clientId = '135811000660772952';
$person = $obj->person('/api/zxca/interface/cert/apply/person', $clientId, '1821810336186871808');
//$transactionId= $person['data']['transactionId'];
$transactionId = '15431789235962404408';


$face = $obj->face('/api/zxca/interface/cert/face/url', $transactionId, '杜星宇', 'identity_card', '511321199109298075');
//print_r($face);exit();


//$person=$obj->ocr('/api/zxca/interface/cert/photo/ocr',$transactionId,);
//print_r($person);exit();
//

//$operatorUserTransactionId= $person['data']['transactionId'];
$operatorUserTransactionId = '15431789235962404408';

$company = $obj->company('/api/zxca/interface/cert/apply/company', $operatorUserTransactionId, '1900512338407194624');
print_r($company);
exit();



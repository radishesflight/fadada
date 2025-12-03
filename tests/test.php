<?php

use GuzzleHttp\Exception\GuzzleException;

require_once __DIR__ . '/../vendor/autoload.php';

$obj = (new \RadishesFlight\Fadada\Sign())->init();

//企业认证
$clientId = '135811000660772953';
$corpFullName = '深圳法大豸网络科技有限公司';
$corpUnifiedIdentifier = '91620111399342191T';
$legalRepName = '萧粞啧';
$certAlg = 'SM2';
try {
    $company = $obj->raCompany('https://dss-uat.fadada.com/api/zxca/ra/cert/apply/company', $clientId, $corpFullName, $corpUnifiedIdentifier, $legalRepName, $certAlg);
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
    $seal = $obj->sealCompany('https://dss-uat.fadada.com/api/seal/create/company', $sealStyle, $sealText, $sealSize, $sealColor, $sealHorizontalText, $sealBottomText, $sealTag, $sealName, $clientId);
    print_r($seal);

} catch (\GuzzleHttp\Exception\GuzzleException $e) {

}
exit();

//上传文件
$file = __DIR__ . '/1.pdf';
$fileUrl = '';
$fileName = '1.pdf';

try {
    $fileResponse = $obj->upload('https://dss-uat.fadada.com/api/file/upload', $file, $fileUrl, $fileName);
    print_r($fileResponse);

} catch (\GuzzleHttp\Exception\GuzzleException $e) {
print_r($e->getMessage());
}
exit();

$clientId = '135811000660772952';
$person = $obj->person('https://dss-uat.fadada.com/api/zxca/interface/cert/apply/person', $clientId, '1821810336186871808');
//$transactionId= $person['data']['transactionId'];
$transactionId = '15431789235962404408';


$face = $obj->face('https://dss-uat.fadada.com/api/zxca/interface/cert/face/url', $transactionId, '杜星宇', 'identity_card', '511321199109298075');
//print_r($face);exit();


//$person=$obj->ocr('https://dss-uat.fadada.com/api/zxca/interface/cert/photo/ocr',$transactionId,);
//print_r($person);exit();
//

//$operatorUserTransactionId= $person['data']['transactionId'];
$operatorUserTransactionId = '15431789235962404408';

$company = $obj->company('https://dss-uat.fadada.com/api/zxca/interface/cert/apply/company', $operatorUserTransactionId, '1900512338407194624');
print_r($company);
exit();



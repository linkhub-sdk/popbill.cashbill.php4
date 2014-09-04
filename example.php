<?php

require_once 'PopbillCashbill.php';


$LinkID = 'TESTER';
$SecretKey = 'huf38wRpmUUdJuHAEXaeTgBbLE8SLUNPERxW3Fy7mL8=';

$json_encoder = new Services_JSON();

$CashbillService = new CashbillService($LinkID,$SecretKey);
$CashbillService->IsTest(true);

$result = $CashbillService->GetUnitCost('1231212312');
if(is_a($result,'PopbillException')) {
	echo $result->__toString();
	exit();
}
else {
	echo $result;
	echo chr(10);
}

echo substr($CashbillService->GetPopbillURL('1231212312','hklee0002','LOGIN'),0,50). ' ...';
echo chr(10);

echo $CashbillService->GetBalance('1231212312');
echo chr(10);
echo $CashbillService->GetPartnerBalance('1231212312');
echo chr(10);

echo substr($CashbillService->GetURL('1231212312','hklee0002','SBOX'),0,50). ' ...';
echo chr(10);

$InUse = $CashbillService->CheckMgtKeyInUse('1231212312','123123');
echo $InUse ? '사용중':'미사용중';
echo chr(10);

$Cashbill = new Cashbill();

$Cashbill->mgtKey = '123123';
$Cashbill->tradeType = '승인거래'; // 승인거래 or 취소거래
$Cashbill->franchiseCorpNum = '1231212312';
$Cashbill->franchiseCorpName = '발행자 상호';
$Cashbill->franchiseCEOName = '발행자 대표자명';
$Cashbill->franchiseAddr = '발행자 주소';
$Cashbill->franchiseTEL = '070-1234-1234';
$Cashbill->identityNum = '01041680206';
$Cashbill->customerName = '고객명';
$Cashbill->itemName = '상품명';
$Cashbill->orderNumber = '주문번호';
$Cashbill->email = 'test@test.com';
$Cashbill->hp = '111-1234-1234';
$Cashbill->fax = '777-444-3333';
$Cashbill->serviceFee = '0';
$Cashbill->supplyCost = '10000';
$Cashbill->tax = '1000';
$Cashbill->totalAmount = '11000';
$Cashbill->tradeUsage = '소득공제용'; //소득공제용 or 지출증빙용
$Cashbill->taxationType = '과세'; // 과세 or 비과세

$Cashbill->smssendYN = false;
$Cashbill->faxsendYN = false;

$result = $CashbillService->Register('1231212312',$Cashbill);
echo $result->message;
echo chr(10);

//exit();


$result = $CashbillService->Update('1231212312','123123',$Cashbill);
echo $result->message;
echo chr(10);

$result = $CashbillService->GetDetailInfo('1231212312','123123');
var_dump($result);
echo chr(10);

$result = $CashbillService->Issue('1231212312','123123','발행 메모');
echo $result->message;
echo chr(10);

$result = $CashbillService->GetInfo('1231212312','123123');
if(is_a($result,'PopbillException')) {
	var_dump($result);
	exit();
}
else {
	var_dump($result);
	echo chr(10);
}
$result = $CashbillService->SendEmail('1231212312','123123','test@test.com');
echo $result->message;
echo chr(10);

$result = $CashbillService->GetLogs('1231212312','123123');
if(is_a($result,'PopbillException')) {
	echo $result->__toString();
	exit();
}
else {
	var_dump($result);
	echo chr(10);
}

$result = $CashbillService->CancelIssue('1231212312','123123','발행취소 메모');
echo $result->message;
echo chr(10);

$result = $CashbillService->Delete('1231212312','123123');
echo $result->message;
echo chr(10);


?>

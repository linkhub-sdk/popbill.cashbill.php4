<?php
/**
* =====================================================================================
* Class for base module for Popbill API SDK. It include base functionality for
* RESTful web service request and parse json result. It uses Linkhub module
* to accomplish authentication APIs.
*
* This module uses curl and openssl for HTTPS Request. So related modules must
* be installed and enabled.
*
* http://www.linkhub.co.kr
* Author : Kim Seongjun (pallet027@gmail.com)
* Written : 2014-04-15
*
* Thanks for your interest.
* We welcome any suggestions, feedbacks, blames or anything.
* ======================================================================================
*/
require_once 'Popbill/popbill.php';

class CashbillService extends PopbillBase {
	
	function CashbillService($LinkID,$SecretKey) {
    	parent::PopbillBase($LinkID,$SecretKey);
    	$this->AddScope('140');
    }
    
    //팝빌 현금영수증 연결 url
    function GetURL($CorpNum,$UserID,$TOGO) {
    	$result = $this->executeCURL('/Cashbill/?TG='.$TOGO,$CorpNum,$UserID);
    	if(is_a($result,'PopbillException')) return $result;
    	
    	return $result->url;
    }
    
    //관리번호 사용여부 확인
    function CheckMgtKeyInUse($CorpNum,$MgtKey) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		return new PopbillException('{"code" : -99999999 , "message" : "관리번호가 입력되지 않았습니다."}');
    	}
    	
    	$response = $this->executeCURL('/Cashbill/'.$MgtKey,$CorpNum);
    	
    	if(is_a($response,'PopbillException')) {
    		if($response->code == -14000003) { return false;}
    		return $response;
    	}
    	else {
    		return is_null($response->itemKey) == false;
    	}
    }
    
    //임시저장
    function Register($CorpNum, $Cashbill, $UserID = null) {
    	$postdata = $this->Linkhub->json_encode($Cashbill);
    	return $this->executeCURL('/Cashbill',$CorpNum,$UserID,true,null,$postdata);
    }    
    
    //삭제
    function Delete($CorpNum,$MgtKey,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		return new PopbillException('{"code" : -99999999 , "message" : "관리번호가 입력되지 않았습니다."}');
    	}
    	return $this->executeCURL('/Cashbill/'.$MgtKey, $CorpNum, $UserID, true,'DELETE','');
    }
    
    //수정
    function Update($CorpNum,$MgtKey,$Cashbill, $UserID = null, $writeSpecification = false) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		return new PopbillException('{"code" : -99999999 , "message" : "관리번호가 입력되지 않았습니다."}');
    	}
    	if($writeSpecification) {
    		$Cashbill->writeSpecification = $writeSpecification;
    	}
    	
    	$postdata = $this->Linkhub->json_encode($Cashbill);
    	return $this->executeCURL('/Cashbill/'.$MgtKey, $CorpNum, $UserID, true, 'PATCH', $postdata);
    }
    
    //발행
    function Issue($CorpNum,$MgtKey,$Memo = '', $UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		return new PopbillException('{"code" : -99999999 , "message" : "관리번호가 입력되지 않았습니다."}');
    	}
    	$Request = new IssueRequest();
    	$Request->memo = $Memo;
    	$postdata = $this->Linkhub->json_encode($Request);
    	
    	return $this->executeCURL('/Cashbill/'.$MgtKey, $CorpNum, $UserID, true,'ISSUE',$postdata);
    }
    
    //발행취소
    function CancelIssue($CorpNum,$MgtKey,$Memo = '', $UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		return new PopbillException('{"code" : -99999999 , "message" : "관리번호가 입력되지 않았습니다."}');
    	}
    	$Request = new MemoRequest();
    	$Request->memo = $Memo;
    	$postdata = $this->Linkhub->json_encode($Request);
    	
    	return $this->executeCURL('/Cashbill/'.$MgtKey, $CorpNum, $UserID, true,'CANCELISSUE',$postdata);
    }
    
    
    //알림메일 재전송
    function SendEmail($CorpNum,$MgtKey,$Receiver, $UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		return new PopbillException('{"code" : -99999999 , "message" : "관리번호가 입력되지 않았습니다."}');
    	}
    	
    	$Request = array('receiver' => $Receiver);
    	$postdata = $this->Linkhub->json_encode($Request);
    	
    	return $this->executeCURL('/Cashbill/'.$MgtKey, $CorpNum, $UserID, true,'EMAIL',$postdata);
    }
    
    //알림문자 재전송
    function SendSMS($CorpNum,$MgtKey,$Sender,$Receiver,$Contents,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		return new PopbillException('{"code" : -99999999 , "message" : "관리번호가 입력되지 않았습니다."}');
    	}
    	
    	$Request = array('receiver' => $Receiver,'sender'=>$Sender,'contents' => $Contents);
    	$postdata = $this->Linkhub->json_encode($Request);
    	
    	return $this->executeCURL('/Cashbill/'.$MgtKey, $CorpNum, $UserID, true,'SMS',$postdata);
    }
    
    //알림팩스 재전송
    function SendFAX($CorpNum,$MgtKey,$Sender,$Receiver,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		return new PopbillException('{"code" : -99999999 , "message" : "관리번호가 입력되지 않았습니다."}');
    	}
    	
    	$Request = array('receiver' => $Receiver,'sender'=>$Sender);
    	$postdata = $this->Linkhub->json_encode($Request);
    	
    	return $this->executeCURL('/Cashbill/'.$MgtKey, $CorpNum, $UserID, true,'FAX',$postdata);
    }
    
    //현금영수증 요약정보 및 상태정보 확인
    function GetInfo($CorpNum,$MgtKey) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		return new PopbillException('{"code" : -99999999 , "message" : "관리번호가 입력되지 않았습니다."}');
    	}
    	return $this->executeCURL('/Cashbill/'.$MgtKey, $CorpNum);
    }
    
    //현금영수증 상세정보 확인 
    function GetDetailInfo($CorpNum,$MgtKey) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		return new PopbillException('{"code" : -99999999 , "message" : "관리번호가 입력되지 않았습니다."}');
    	}
    	return $this->executeCURL('/Cashbill/'.$MgtKey.'?Detail', $CorpNum);
    }
    
    //현금영수증 요약정보 다량확인 최대 1000건
    function GetInfos($CorpNum,$MgtKeyList = array()) {
    	if(is_null($MgtKeyList) || empty($MgtKeyList)) {
    		return new PopbillException('{"code" : -99999999 , "message" : "관리번호가 입력되지 않았습니다."}');
    	}
    	
    	$postdata = $this->Linkhub->json_encode($MgtKeyList);
    	
    	return $this->executeCURL('/Cashbill/States', $CorpNum, null, true,null,$postdata);
    }
    
    //현금영수증 문서이력 확인 
    function GetLogs($CorpNum,$MgtKey) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		return new PopbillException('{"code" : -99999999 , "message" : "관리번호가 입력되지 않았습니다."}');
    	}
    	return $this->executeCURL('/Cashbill/'.$MgtKey.'/Logs', $CorpNum);
    }
    
    //팝업URL
    function GetPopUpURL($CorpNum,$MgtKey,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		return new PopbillException('{"code" : -99999999 , "message" : "관리번호가 입력되지 않았습니다."}');
    	}
    	
    	$result = $this->executeCURL('/Cashbill/'.$MgtKey.'?TG=POPUP', $CorpNum,$UserID);
    	if(is_a($result,'PopbillException')) return $result;
    	
    	return $result->url;
    }
    
    //인쇄URL
    function GetPrintURL($CorpNum,$MgtKey,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		return new PopbillException('{"code" : -99999999 , "message" : "관리번호가 입력되지 않았습니다."}');
    	}
    	
    	$result = $this->executeCURL('/Cashbill/'.$MgtKey.'?TG=PRINT', $CorpNum,$UserID);
    	if(is_a($result,'PopbillException')) return $result;
    	
    	return $result->url;
    }

    //공급받는자 인쇄URL
    function GetEPrintURL($CorpNum,$MgtKey,$UserID = null) {
        if(is_null($MgtKey) || empty($MgtKey)) {
            return new PopbillException('{"code" : -99999999 , "message" : "관리번호가 입력되지 않았습니다."}');
        }
        
        $result = $this->executeCURL('/Cashbill/'.$MgtKey.'?TG=EPRINT', $CorpNum,$UserID);
        if(is_a($result,'PopbillException')) return $result;
        
        return $result->url;
    }
    
    //공급받는자 메일URL
    function GetMailURL($CorpNum,$MgtKey,$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		return new PopbillException('{"code" : -99999999 , "message" : "관리번호가 입력되지 않았습니다."}');
    	}
    	
    	$result = $this->executeCURL('/Cashbill/'.$MgtKey.'?TG=MAIL', $CorpNum,$UserID);
    	if(is_a($result,'PopbillException')) return $result;
    	
    	return $result->url;
    }
    
    //현금영수증 다량인쇄 URL
    function GetMassPrintURL($CorpNum,$MgtKeyList = array(),$UserID = null) {
    	if(is_null($MgtKey) || empty($MgtKey)) {
    		return new PopbillException('{"code" : -99999999 , "message" : "관리번호가 입력되지 않았습니다."}');
    	}
    	
    	$postdata = $this->Linkhub->json_encode($MgtKeyList);
    	
    	$result = $this->executeCURL('/Cashbill/Prints', $CorpNum, $UserID, true,null,$postdata);
    	if(is_a($result,'PopbillException')) return $result;
    	
    	return $result->url;
    }
    
    //발행단가 확인
    function GetUnitCost($CorpNum) {
    	$result = $this->executeCURL('/Cashbill?cfg=UNITCOST', $CorpNum);
    	if(is_a($result,'PopbillException')) return $result;
    	
    	return $result->unitCost;
    }
    
    
}

class Cashbill
{
	
	var $mgtKey;
	
    var $tradeDate;
    var $tradeUsage;
    var $tradeType;
    
    var $taxationType;
    var $supplyCost;
    var $tax;
    var $serviceFee;
    var $totalAmount;
    
    var $franchiseCorpNum;
    var $franchiseCorpName;
    var $franchiseCEOName;
    var $franchiseAddr;
    var $franchiseTEL;
    
    var $identityNum;
    var $customerName;
    var $itemName;
    var $orderNumber;
    
    var $email;
    var $hp;
    var $fax;
    var $smssendYN;
    var $faxsendYN;
    
    var $orgConfirmNum;
	
}

class MemoRequest {
	var $memo;
}
class IssueRequest {
	var $memo;
}
?>

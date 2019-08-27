<script type="text/javascript">
  var queryAppendTxt = "";
</script>

<?php
function clear(){


  if(isset($_POST)){
    $_POST= array();
  }
  if(isset($_GET))
    $_GET = array();
}

?>
<?php 


if ( isset($_POST["submit"])):

  ?>
  <?php  
  $cat = $_POST["Category"];
  $code = "";  
 
  if(isset($_POST["loc"]))
    {$locOption = $_POST["loc"];

if($locOption=="curr"){
    $code = $_POST["hereZip"];
} else
{
    $code = $_POST["givenZip"];
}
}


?>


<?php 
if(isset($_POST["Used"]))
{
 $usedvar= $_POST["Used"];
}


$endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';  // URL to call
$version = '1.0.0';  // API version supported by your application
$appid = 'Your APP ID here';  // Replace with your own AppID
$globalid = 'EBAY-US';  // Global ID of the eBay site you want to search (e.g., EBAY-DE)
$responseEncoding = 'JSON';
$query = $_POST["keyword"];  // You may want to supply your own query
$safequery = urlencode($query);
if($code)
$codesafe = urlencode($code);
if($cat!=""){
  $safecat = urlencode($cat);
}
$i = '0'; 
$filterarray =
array(
  array(
    'name' => 'HideDuplicateItems',
    'value' => 'true',
    'paramName' => '',
    'paramValue' => ''),
);

$dist = "";

if(isset($_POST["NearBySearch"]))
{
 
  if(isset($_POST["dist"])){

    $dist= $_POST["dist"];
    $distarr =array(
      'name' => 'MaxDistance',
      'value' => trim($dist),
      'paramName' => '',
      'paramValue' => '');

    array_push($filterarray,$distarr );
}

}



$conditionval = array();
// dynamic filter creation
foreach ($_POST as $key =>$value){

  switch ($key) {
    case "New":
      array_push($conditionval, $value);
    break;

      case "Used":
    array_push($conditionval, $value);
    break;

    case "Unspecified":
      array_push($conditionval, $value);
       break;

    case "FreeShippingOnly":
      $freeShip = array( 
        'name' => $key,
        'value' => $value,
        'paramName' => '',
        'paramValue' => ''

    );
    array_push($filterarray,$freeShip );
    break;
    case "LocalPickupOnly":
      $localArr = array(
        'name' => $key,
        'value' => $value,
        'paramName' => '',
        'paramValue' => '');
      array_push($filterarray,$localArr );
      break;
    
}


}

if(!empty($conditionval)){

  $condarr = array(
    'name' => 'Condition',
    'value' => $conditionval,
    'paramName' => '',
    'paramValue' => '');

  array_push($filterarray,$condarr );

}


function buildURLArray ($filterarray) {
  global $urlfilter;
  global $i;
  // Iterate through each filter in the array
  foreach($filterarray as $itemfilter) {
    // Iterate through each key in the filter
    foreach ($itemfilter as $key =>$value) {
      if(is_array($value)) {
        foreach($value as $j => $content) { // Index the key for each value
          $urlfilter .= "&itemFilter($i).$key($j)=$content";
      }
  }
  else {
    if($value != "") {
      $urlfilter .= "&itemFilter($i).$key=$value";
  }
}
}
$i++;
}

  //echo $urlfilter;
return "$urlfilter";
} 
buildURLArray($filterarray);

// Construct the findItemsByKeywords HTTP GET call 
$apicall = "$endpoint?";
$apicall .= "OPERATION-NAME=findItemsAdvanced";
$apicall .= "&SERVICE-VERSION=$version";
$apicall .= "&SECURITY-APPNAME=$appid";
$apicall .= "&GLOBAL-ID=$globalid";
$apicall .= "&keywords=$safequery";
if(isset($_POST["NearBySearch"]))
  $apicall .= "&buyerPostalCode=$codesafe";
if($cat!=""){
  $apicall.="&categoryId=$safecat";
}
$apicall .= "&paginationInput.entriesPerPage=20";
$apicall .= "$urlfilter";
$apicall .= "&RESPONSE-DATA-FORMAT=$responseEncoding";

$resp = "q";
$resp = file_get_contents($apicall);

?>
<script type="text/javascript">
  var jsonObjFull = <?php echo $resp ?>;
   

var myTable = "";
var intermediateJSON = jsonObjFull.findItemsAdvancedResponse[0];
if(intermediateJSON.ack[0]=="Failure"){
      myTable = document.createElement("div");
      myTable.style.border= "1px solid grey";
      myTable.style.width = "50%";
      myTable.style.margin = "auto";
      myTable.style.textalign = "center";
      myTable.style.backgroundColor = "#D0D0D0";
      myTable.setAttribute('id','idMyTable');
      var errortext = intermediateJSON.errorMessage[0].error[0].message[0];
      myTable.innerHTML= "<h3>"+errortext+"</h3>";
    
}
else{
    var postJSON =  <?php echo json_encode($_POST, JSON_PRETTY_PRINT) ?>;

  
    if(postJSON.keyword){
      queryAppendTxt += "&keyword="+postJSON.keyword;
    }
    if(postJSON.Category)
    {
      queryAppendTxt += "&Category="+postJSON.Category;
    }
    if(postJSON.loc)
    {
      queryAppendTxt += "&loc="+postJSON.loc;
    }
    if(postJSON.hereZip)
    {
      queryAppendTxt += "&hereZip="+postJSON.hereZip;
    }
    if(postJSON.givenZip)
    {
      queryAppendTxt += "&givenZip="+postJSON.givenZip;
    }
    if(postJSON.curr)
    {
      queryAppendTxt += "&curr="+postJSON.curr;
    }
    if(postJSON.New)
    {
      queryAppendTxt += "&New="+postJSON.New;
    }
    if(postJSON.Used)
    {
      queryAppendTxt += "&Used="+postJSON.Used;
    }
    if(postJSON.Unspecified)
    {
      queryAppendTxt += "&Unspecified="+postJSON.Unspecified;
    }
    if(postJSON.FreeShippingOnly)
    {
      queryAppendTxt += "&FreeShippingOnly="+postJSON.FreeShippingOnly;
    }
    if(postJSON.LocalPickupOnly)
    {
      queryAppendTxt += "&LocalPickupOnly="+postJSON.LocalPickupOnly;
    }
    if(postJSON.dist)
    {
      queryAppendTxt += "&dist="+postJSON.dist;
    }
    if(postJSON.NearBySearch)
    {
      queryAppendTxt += "&NearBySearch="+postJSON.NearBySearch;
    }

    
    var jsonObj = jsonObjFull.findItemsAdvancedResponse[0].searchResult[0];
    myTable = document.createElement("table");
    myTable.setAttribute('id','idMyTable');
    myTable.setAttribute('border','1');  
    myTable.style.borderColor = "grey";
    var tabHead = myTable.createTHead();       
    var headRow = tabHead.insertRow(0);
    var cell1 = headRow.insertCell(0);
    cell1.style.fontWeight="bold";
    cell1.innerHTML = "Index";
    cell1.style.textAlign = "center";

    var cell2 = headRow.insertCell(1);
    cell2.style.fontWeight="bold";
    cell2.innerHTML = "Photo";
    cell2.style.textAlign = "center";

    var cell3 = headRow.insertCell(2);
    cell3.style.fontWeight="bold";
    cell3.innerHTML = "Name";
    cell3.style.textAlign = "center";

    var cell7 = headRow.insertCell(3);
    cell7.style.fontWeight="bold";
    cell7.innerHTML = "Price";
    cell7.style.textAlign = "center";
    

    var cell4 = headRow.insertCell(4);
    cell4.style.fontWeight="bold";
    cell4.innerHTML = "Zipcode";
    cell4.style.textAlign = "center";

    var cell5 = headRow.insertCell(5);
    cell5.style.fontWeight="bold";
    cell5.innerHTML = "Condition";
    cell5.style.textAlign = "center";

    var cell6 = headRow.insertCell(6);
    cell6.style.fontWeight="bold";
    cell6.innerHTML = "Shipping Option";
    cell6.style.textAlign = "center";


    try{
      if(jsonObj){
      
        var rowData = jsonObj.item;
            for(var j=0;j<rowData.length;j++){

              var rowObj = rowData[j];
              var tempRow = myTable.insertRow(j+1);
              var indexCell = ""+(j+1);
              var tempCell1 =  tempRow.insertCell(0);
              tempCell1.innerHTML= indexCell;

             
              var imageCell = tempRow.insertCell(1);
              imageCell.style.textAlign = "center";
              if(rowObj.galleryURL){
              var imgTemp = document.createElement("img");
              imgTemp.setAttribute('src',rowObj.galleryURL);
              imgTemp.style.height= "70px";
              imgTemp.style.width = "85px";
              imageCell.appendChild(imgTemp);
            }else{
              imageCell.innerHTML = "N/A";
            }
          //------------------------------------------
              var anchorTemp = document.createElement('a');
              if(rowObj.title){
                var titleCell = rowObj.title;
                var tempCell7 = tempRow.insertCell(2);  
                anchorTemp.style.whiteSpace= "nowrap";
                var queryItm = "?itemDetId="+rowObj.itemId+queryAppendTxt;
                anchorTemp.innerText = titleCell;
                anchorTemp.setAttribute('href', queryItm);
              }else{
                anchorTemp.innerText = "N/A";
              }  
              tempCell7.appendChild(anchorTemp);

              if(rowObj.sellingStatus){
                if(rowObj.sellingStatus[0].currentPrice)
                  var priceCell =  "$"+rowObj.sellingStatus[0].currentPrice[0].__value__ ;
                else
                  var priceCell = "N/A";   
              }else
                 var priceCell = "N/A";   
              var tempCell2 =  tempRow.insertCell(3);
              tempCell2.innerHTML= priceCell;
              

              if(rowObj.postalCode)
                var codeCell =  rowObj.postalCode;
              else
                var codeCell =  "N/A";
              var tempCell3 =  tempRow.insertCell(4);
              tempCell3.innerHTML= codeCell;
              if(rowObj.condition)
                var conditionCell =  rowObj.condition[0].conditionDisplayName[0];//condition.conditionDisplayName;
              else
                var conditionCell = "N/A";  
              var tempCell4 =  tempRow.insertCell(5);
              tempCell4.innerHTML= conditionCell;

              var shppingCell= "";
            
              if(rowObj.shippingInfo){
              var tempShip =  rowObj.shippingInfo[0].shippingServiceCost;//shippingInfo.shippingType;
              if(tempShip){
                if(tempShip[0].__value__ != "0.0")
                  shppingCell = "$" + tempShip[0].__value__ ;
                else
                  shppingCell = "Free Shipping";

              }
              else
                shppingCell ="N/A";
              }
              else
              shppingCell ="N/A";  
              var tempCell4 =  tempRow.insertCell(6);
              tempCell4.innerHTML= shppingCell;



          }

      }
     }catch(err){
      myTable = document.createElement("div");
      myTable.style.border= "1px solid grey";
      myTable.style.width = "50%";
      myTable.style.margin = "auto";
      myTable.style.textalign = "center";
      myTable.style.backgroundColor = "#D0D0D0";
      myTable.innerHTML= "<h3>No Records have been found</h3>";
      myTable.setAttribute("id", "errorDiv");
     
     } 

   }  



</script>
<?php endif;?>

<?php

if ( isset($_GET["itemDetId"])):
  $itemID = $_GET["itemDetId"];


  ?>

  <?php

  $s_endpoint = 'http://open.api.ebay.com/shopping'; 
  $responseEncoding = 'JSON';
  $s_version = '967';
 $appID = 'Pankhuri-FindingP-PRD-e16e081a6-08221a17';  // Replace with your own AppID
 $siteID = '0';
 $apiItmcall = "$s_endpoint?callname=GetSingleItem"
 . "&version=$s_version"
 . "&siteid=$siteID"
 . "&appid=$appID"
 . "&ItemID=$itemID"
       . "&IncludeSelector=Description,Details,ItemSpecifics"   // need Details to get MyWorld info
       . "&responseencoding=$responseEncoding";


       $m_endpoint = 'http://svcs.ebay.com/MerchandisingService'; 
       $responseEncoding = 'JSON';
 


       $apicallSim = "$m_endpoint?OPERATION-NAME=getSimilarItems"
       . "&SERVICE-VERSION=1.1.0"
       . "&CONSUMER-ID=$appID"
       . "&itemId=$itemID"
       . "&maxResults=8"  
       . "&RESPONSE-DATA-FORMAT=$responseEncoding" ;// need Details to get MyWorld info";


       $respSim = file_get_contents($apicallSim);
       $respo = file_get_contents($apiItmcall);      

       ?>
<script type="text/javascript">
  //---------------------------------------------------item detail code here -----------------------------------
  var jsonItmObj = <?php echo $respo; ?>;
    var itemDetail = "";
  var itemDetailFail = false;
  var sellerMessageFail = false;
  var failurePara = "";
  itemDetail = document.createElement("table");
  var tabCaption = itemDetail.createCaption();
  tabCaption.innerHTML = "<h1>Item Details</h1>";
  itemDetail.style.borderColor="grey";
  itemDetail.setAttribute('id','idItemTable');
  itemDetail.setAttribute('border','1'); 
  itemDetail.setAttribute('width','100%');

  var nextDiv = document.createElement("div");
  nextDiv.setAttribute("id","idNextDiv");
  var msgLabel1 = document.createElement("p");
  msgLabel1.setAttribute("id", "msgLabel1");
  msgLabel1.innerText = "Click here to show Seller Message";

  
 nextDiv.style.textAlign = "center";
 nextDiv.appendChild(msgLabel1);
 var scrollDowImg = document.createElement("img");
 scrollDowImg.setAttribute('src', "http://csci571.com/hw/hw6/images/arrow_down.png");
 scrollDowImg.style.width="30px";
 scrollDowImg.style.height = "20px";
 scrollDowImg.setAttribute("id" , "strech"); 
 nextDiv.appendChild(scrollDowImg);

  if(jsonItmObj){
    
   if(jsonItmObj.Ack === "Failure"){
       itemDetailFail = true;
       failurePara = document.createElement("p");
       failurePara.style.border = "1px solid grey";
       failurePara.style.width = "50%";
       failurePara.style.margin = "auto";
       failurePara.setAttribute("id","failPara1");
       failurePara.style.backgroundColor = "#D0D0D0";
       var failureLabel = "No details found";
       if(jsonItmObj.Errors){
        failureLabel = jsonItmObj.Errors[0].LongMessage;
       }
       failurePara.innerText = failureLabel;
        sellerMessageFail = true;
        var divErrorMessge = document.createElement("div");
        divErrorMessge.innerHTML= "<h2>No Seller Message Found</h2>";
        divErrorMessge.style.backgroundColor = "#D0D0D0";
        divErrorMessge.setAttribute("id" , "idiframeObj");
        divErrorMessge.style.display = "none";
        var centerObj = document.createElement("center");
        divErrorMessge.style.border="1px solid grey";
        divErrorMessge.style.width="50%";
        centerObj.appendChild(divErrorMessge);
        nextDiv.appendChild(centerObj);

       
   }else{
            var getJSON =  <?php echo json_encode($_GET, JSON_PRETTY_PRINT) ?>;

            if(getJSON.keyword){
              queryAppendTxt += "&keyword="+getJSON.keyword;
            }
            if(getJSON.Category)
            {
              queryAppendTxt += "&Category="+getJSON.Category;
            }
            if(getJSON.loc)
            {
              queryAppendTxt += "&loc="+getJSON.loc;
            }
            if(getJSON.hereZip)
            {
              queryAppendTxt += "&hereZip="+getJSON.hereZip;
            }
            if(getJSON.givenZip)
            {
              queryAppendTxt += "&givenZip="+getJSON.givenZip;
            }
            if(getJSON.curr)
            {
              queryAppendTxt += "&curr="+getJSON.curr;
            }
            if(getJSON.New)
            {
              queryAppendTxt += "&New="+getJSON.New;
            }
            if(getJSON.Used)
            {
              queryAppendTxt += "&Used="+getJSON.Used;
            }
            if(getJSON.Unspecified)
            {
              queryAppendTxt += "&Unspecified="+getJSON.Unspecified;
            }
            if(getJSON.FreeShippingOnly)
            {
              queryAppendTxt += "&FreeShippingOnly="+getJSON.FreeShippingOnly;
            }
            if(getJSON.LocalPickupOnly)
            {
              queryAppendTxt += "&LocalPickupOnly="+ getJSON.LocalPickupOnly;
            }
            if(getJSON.dist)
            {
              queryAppendTxt += "&dist="+getJSON.dist;
            }
            if(getJSON.NearBySearch)
            {
              queryAppendTxt += "&NearBySearch="+getJSON.NearBySearch;
            }

            var itmObj = jsonItmObj.Item;
            var countRow =0;
            if(itmObj){

                if(itmObj.PictureURL){

                    var tempRow0 = itemDetail.insertRow(countRow);
                    var photoLabel = "Photo";
                    var tempCell11 = tempRow0.insertCell(0);
                    tempCell11.style.fontWeight = "bold";
                    tempCell11.innerHTML = photoLabel;
                    tempCell11.style.whiteSpace= "nowrap";


                    var itmImgCell = tempRow0.insertCell(1);
                    itmImgCell.style.textAlign = "center";
                    var itmImg = document.createElement("img");
                    itmImg.setAttribute('src',itmObj.PictureURL);
                    itmImg.style.width="115px";
                    itmImg.style.height = "205px";
                    itmImgCell.appendChild(itmImg);
                    countRow++;

              }

            if(itmObj.Title){  
                  var tempRow1 = itemDetail.insertRow(countRow);
                  var titleLabel = "Title";
                  var tempCell12 = tempRow1.insertCell(0);
                  tempCell12.style.fontWeight = "bold";
                  tempCell12.innerHTML = titleLabel;

                  var imgTitle = itmObj.Title;
                  var tempCell13 = tempRow1.insertCell(1);
                  tempCell13.innerHTML = imgTitle;
                  countRow++;

            }  


            var itmSubTitle = itmObj.Subtitle;
            if(itmSubTitle){
                  
                  var tempRow2 = itemDetail.insertRow(countRow);
                  var subtitleLabel = "SubTitle";
                  var tempCell13 = tempRow2.insertCell(0);
                  tempCell13.style.fontWeight = "bold";
                  tempCell13.innerHTML = subtitleLabel;

                  
                  var tempCell14 = tempRow2.insertCell(1);
                  tempCell14.innerHTML = itmSubTitle;
                  countRow++;

             }     


            if(itmObj.CurrentPrice){
                var tempRow3 = itemDetail.insertRow(countRow);
                var priceLabel = "Price";
                var tempCell15 = tempRow3.insertCell(0);
                tempCell15.style.fontWeight = "bold";
                tempCell15.innerHTML = priceLabel;

                var itmPrice = " "+itmObj.CurrentPrice.Value + " " + itmObj.CurrentPrice.CurrencyID ;
                var tempCell16 = tempRow3.insertCell(1);
                tempCell16.innerHTML = itmPrice;


                countRow++;


             }   

            if(itmObj.Location){ 
                var tempRow4 = itemDetail.insertRow(countRow);
                var locLabel = "Location";
                var tempCell17 = tempRow4.insertCell(0);
                tempCell17.style.fontWeight = "bold";
                tempCell17.innerHTML = locLabel;

                var itmLocation = itmObj.Location;
                var tempCell18 = tempRow4.insertCell(1);
                tempCell18.innerHTML = itmLocation;

                countRow++;

          }
            if(itmObj.Seller.UserID){

            var tempRow5 = itemDetail.insertRow(countRow);
            var sellLabel = "Seller";
            var tempCell19 = tempRow5.insertCell(0);
            tempCell19.style.fontWeight = "bold";
            tempCell19.innerHTML = sellLabel;

            var itmSeller = itmObj.Seller.UserID;
            var tempCell20 = tempRow5.insertCell(1);
            tempCell20.innerHTML = itmSeller;

            countRow++;

          }

          if(itmObj.ReturnPolicy){
              var tempRow6 = itemDetail.insertRow(countRow);
              var retLabel = "Return Policy(US)";
              var tempCell21 = tempRow6.insertCell(0);
              tempCell21.style.fontWeight = "bold";
              tempCell21.innerHTML = retLabel;
              var itmRet="";
              try{
              if(itmObj.ReturnPolicy.ReturnsAccepted === "Return Accepted" || itmObj.ReturnPolicy.ReturnsAccepted === "Returns Accepted" )
               itmRet  = ""+itmObj.ReturnPolicy.ReturnsAccepted + " within  "+ itmObj.ReturnPolicy.ReturnsWithin ;
              else
               itmRet = ""+itmObj.ReturnPolicy.ReturnsAccepted;
            }catch(err){
                itmRet="N/A";
            }   
              var tempCell22 = tempRow6.insertCell(1);
              tempCell22.innerHTML = itmRet; // fix this @pankhuri

          }


           var itmSpec = itmObj.ItemSpecifics;
          
            if(itmSpec){
              var specList  = itmSpec.NameValueList;
              for(var k =0; k<specList.length;k++){
                  countRow++;
                  var tempRoww = itemDetail.insertRow(countRow);
                  var rowLab = specList[k].Name;
                  var tempSpecCell = tempRoww.insertCell(0);
                  tempSpecCell.style.fontWeight = "bold";
                  tempSpecCell.innerHTML = rowLab;

                  var vals = specList[k].Value;
                  if(vals)
                   var val = vals[0];
                  else
                    var val = "N/A"; 
                  var  tempValCell = tempRoww.insertCell(1);
                  tempValCell.innerHTML = val; 
            }


           } 

           if(itmObj.Description){
                  if(itmObj.Description!="")
                   {
                       var iframeObj = document.createElement("iframe");
                       iframeObj.setAttribute("srcdoc", itmObj.Description); 
                       iframeObj.style.width = "80%";
                       iframeObj.setAttribute("align", "middle");
                       iframeObj.style.display= "none";
                       iframeObj.style.textalign = "center";
                       iframeObj.textalign= "center";
                       iframeObj.setAttribute("id","idiframeObj");
                       iframeObj.setAttribute("frameBorder","0");
                       var centerObj = document.createElement("center");
                       centerObj.appendChild(iframeObj);
                       nextDiv.appendChild(centerObj);
                       nextDiv.style.textAlign = "center";
                     }else{

                      sellerMessageFail = true;
                      var divErrorMessge = document.createElement("div");
                      divErrorMessge.innerHTML= "<h2>No Seller Message Found</h2>";
                      divErrorMessge.setAttribute("id" , "idiframeObj");
                      divErrorMessge.style.display = "none";
                      divErrorMessge.style.border= "1px solid grey";
                      divErrorMessge.style.width="50%";
                      var centerObj = document.createElement("center");
                      centerObj.appendChild(divErrorMessge);
                      nextDiv.appendChild(centerObj);

                     }  
           

           }else{

              sellerMessageFail = true;
              var divErrorMessge = document.createElement("div");
              divErrorMessge.innerHTML= "<h2>No Seller Message Found</h2>";
              divErrorMessge.setAttribute("id" , "idiframeObj");
              divErrorMessge.style.display = "none";
              var centerObj = document.createElement("center");
              divErrorMessge.style.border="1px solid grey";
              divErrorMessge.style.width="50%";
              centerObj.appendChild(divErrorMessge);
              nextDiv.appendChild(centerObj);
           

           }
           

       }

          var simSuccess = true;
          var outDiv = "";
          outDiv = document.createElement("div");
          var simJSONObj = <?php echo $respSim;?> 
          if(simJSONObj){


              var simItmResp = simJSONObj.getSimilarItemsResponse;
\              if(simItmResp.ack === "Success"){

                var simItmDet = "";
                
                outDiv.style.width = "50%";
                outDiv.setAttribute("id","idOutDiv");
                outDiv.style.border = "1px solid grey";
                outDiv.style.margin = "auto";
                outDiv.style.overflowX = "auto";
                simItmDet = document.createElement("table");
                simItmDet.setAttribute('id','idSimItemTable');

                var divRow = simItmDet.insertRow(0);

                var simItmData = simItmResp.itemRecommendations.item;

                if(!simItmData || simItmData.length==0)
                  simSuccess = false;

              for(var u =0;u< simItmData.length;u++){
                  var simItmObj = simItmData[u];

                  var tempCellSim = divRow.insertCell(u);
                  var tempDiv = document.createElement("div");

                  //debugger;
                  var simItmImg = document.createElement("img");
                  if(simItmObj.imageURL)
                    simItmImg.setAttribute('src',simItmObj.imageURL);

                  simItmImg.style.width="115px";
                  simItmImg.style.height = "180px";
                  tempDiv.appendChild(simItmImg);

                  var simtitle = simItmObj.title;
                  var aSimTemp = document.createElement('a');
                  if(simtitle){
                    
                    aSimTemp.innerText = simtitle;
                    var simQuItm = "?itemDetId="+simItmObj.itemId+queryAppendTxt;
                    aSimTemp.setAttribute('href', simQuItm);
                  }else{

                    aSimTemp.innerText="N/A";
                    aSimTemp.setAttribute('href','');
                   } 
                  tempDiv.appendChild(aSimTemp);

                  var par = document.createElement("p");
                  var parInnerTxt;
                 
                  if(simItmObj.buyItNowPrice){
                  parInnerTxt = "$" + simItmObj.buyItNowPrice.__value__;
                  }else{
                    parInnerTxt="N/A";
                  }

                  par.innerText = parInnerTxt;
                  par.style.fontWeight = "bold";
                  tempDiv.appendChild(par);
                  tempCellSim.appendChild(tempDiv);

              }

              outDiv.appendChild(simItmDet);

             }else{
              simSuccess = false;

          }

      }else{
        simSuccess = false;

    }

    if(!simSuccess){

     var simItmFailText = "No Similar Items Found";
     var simItmFailPara = document.createElement("div");
     simItmFailPara.innerHTML = "<h2>"+simItmFailText+"</h2>";
     simItmFailPara.style.border = "1px solid grey";
     outDiv.appendChild(simItmFailPara);  
 }
     var msgLabel2 = document.createElement("p");
     msgLabel2.setAttribute("id", "msgLabel2");
     msgLabel2.innerText = "Click here to show Similar Items";
     if(nextDiv)
        nextDiv.appendChild(msgLabel2);  

    outDiv.style.display= "none";
    var scrollDowImg2 = document.createElement("img");
    scrollDowImg2.setAttribute('src', "http://csci571.com/hw/hw6/images/arrow_down.png");
    scrollDowImg2.style.width="30px";
    scrollDowImg2.style.height = "20px";
    scrollDowImg2.setAttribute("id" , "strech2"); 
    if(nextDiv)
     nextDiv.appendChild(scrollDowImg2);
    if(nextDiv) 
     nextDiv.appendChild(outDiv);

  } 

</script>

<?php endif;?>

<html>
<head>
  <style type="text/css">

  * {
    margin: 6px;
}

hr {
  margin: 0;
  padding: 0;
}

h3{
  text-align: center;
}

#div2 {
    text-align: left;
    width: 600px; 
    margin: auto; 
    border: 1px solid grey; 
    padding: 0 10px; 
    padding-bottom: 25px;
    background: #fafafa;
}

.no-margin {
    margin: 0;
    padding: 0;
}

#idMyTable{

    width : 85%;
    margin: auto;

}
a{
    text-decoration: none;
    color : black;
}

table {
    border-collapse: collapse;
}

 a:hover{

    color : grey;
 }

 #idItemTable{

    width : 40%;
    margin: auto;
 }

 #idSimItemTable td{

    text-align: center;
    padding-left: 20px;
    padding-right: 20px;
    width : 69px;
    font-size: 14px;

 }

</style>  
</head> 



<body onload="getLocation()">
    <div id="div1">  
        <form  method="POST" action="" id="myform" >
            <div id="div2">
                <p style="text-align: center; font-size: 32px;"><i>Product Search</i></p>
                <hr>   
                <b>Keyword</b> <input type= "text" id="hey" name="keyword" required="true" value="<?php if (isset($_POST["keyword"])){echo $_POST["keyword"]; }else{if(isset($_GET["keyword"]))echo $_GET["keyword"];else echo "";} ?>">
                
                <br>
                <b>Category</b>
                <select name="Category" id="catSelect">
                    <option selected=selected value="">All Categories</option>>
                    <option value="550" <?php if(isset($_POST["submit"])){ if(isset($_POST["Category"]) and $_POST["Category"]=="550"){echo 'selected';} else echo '' ; }else{ if(isset($_GET["Category"]) and $_GET["Category"]=="550")echo 'selected';else echo ''; } ?>>Art</option>
                    <option value=" 2984" <?php if(isset($_POST["submit"])){ if(isset($_POST["Category"]) and $_POST["Category"]=="2984"){echo 'selected';} else echo '' ; }else{ if(isset($_GET["Category"]) and $_GET["Category"]=="2984")echo 'selected';else echo ''; } ?>>Baby</option>
                    <option value="267" <?php if(isset($_POST["submit"])){ if(isset($_POST["Category"]) and $_POST["Category"]=="267"){echo 'selected';} else echo '' ; }else{ if(isset($_GET["Category"]) and $_GET["Category"]=="267")echo 'selected';else echo ''; } ?>>Books</option>
                    <option value="11450" <?php if(isset($_POST["submit"])){ if(isset($_POST["Category"]) and $_POST["Category"]=="11450"){echo 'selected';} else echo '' ; }else{ if(isset($_GET["Category"]) and $_GET["Category"]=="11450")echo 'selected';else echo ''; } ?>>Clothing, Shoes & Accessories</option>
                    <option value="58058" <?php if(isset($_POST["submit"])){ if(isset($_POST["Category"]) and $_POST["Category"]=="58058"){echo 'selected';} else echo '' ; }else{ if(isset($_GET["Category"]) and $_GET["Category"]=="58058")echo 'selected';else echo ''; } ?>>Computers/Tablets & Networking</option>
                    <option value="26395" <?php if(isset($_POST["submit"])){ if(isset($_POST["Category"]) and $_POST["Category"]=="26395"){echo 'selected';} else echo '' ; }else{ if(isset($_GET["Category"]) and $_GET["Category"]=="26395")echo 'selected';else echo ''; } ?>>Health & Beauty</option>
                    <option value="11233" <?php if(isset($_POST["submit"])){ if(isset($_POST["Category"]) and $_POST["Category"]=="11233"){echo 'selected';} else echo '' ; }else{ if(isset($_GET["Category"]) and $_GET["Category"]=="11233")echo 'selected';else echo ''; } ?>>Music</option>
                    <option value="1249" <?php if(isset($_POST["submit"])){ if(isset($_POST["Category"]) and $_POST["Category"]=="1249"){echo 'selected';} else echo '' ; }else{ if(isset($_GET["Category"]) and $_GET["Category"]=="1249")echo 'selected';else echo ''; } ?>>Video Games & Consoles</option>
                </select> 
                <br>
                <b>Condition</b>
                <input type="checkbox" name="New" value="New" id="ch1" <?php if(isset($_POST["submit"])){ if(isset($_POST["New"])){echo 'checked';} else echo '' ; }else{ if(isset($_GET["New"]))echo 'checked';else echo ''; } ?> >New</input>

                <input type="checkbox" name="Used" value="Used" id="ch2" <?php if(isset($_POST["submit"])){ if(isset($_POST["Used"])){echo 'checked';} else echo '' ; }else{ if(isset($_GET["Used"]))echo 'checked';else echo ''; } ?> >Used</input>
                
                <input type="checkbox" name="Unspecified" value="Unspecified" id="ch3" <?php if(isset($_POST["submit"])){ if(isset($_POST["Unspecified"])){echo 'checked';} else echo '' ; }else{ if(isset($_GET["Unspecified"]))echo 'checked';else echo ''; } ?>>Unspecified</input>
                <br>
                <b>Shipping options</b>
                <input type="checkbox" name="LocalPickupOnly" id="sh1"value="true"  <?php if(isset($_POST["submit"])){ if(isset($_POST["LocalPickupOnly"])){echo 'checked';} else echo ''; }else{ if(isset($_GET["LocalPickupOnly"]))echo 'checked';else echo ''; } ?>>Local Pickup</input>
<!--                 <input type="checkbox" name="FreeShippingOnly" value="true">Free Shipping</input>
-->            <input type="checkbox" name="FreeShippingOnly" id="sh2" value="true" <?php if(isset($_POST["submit"])){ if(isset($_POST["FreeShippingOnly"])){echo 'checked';} else echo ''; }else{ if(isset($_GET["FreeShippingOnly"]))echo 'checked';else echo ''; } ?>>Free Shipping</input>
                <br>
                <div style="float: left; margin: 0; padding: 0;">
                    <input type="checkbox" name="NearBySearch"id="sh3" value="NearBySearch" onchange="handleChange(event)" <?php if(isset($_POST["submit"])){ if(isset($_POST["NearBySearch"])){echo 'checked';} else echo ''; }else{ if(isset($_GET["NearBySearch"]))echo 'checked';else echo ''; } ?>><b>Enable Nearby Search </b></input>

                    <input id="dist1" type="text" name="dist" size="10" placeholder="10" value= " <?php if(isset($_POST["submit"])){ if(isset($_POST["dist"])){echo $_POST["dist"];} else echo "10"; }else{ if(isset($_GET["dist"]))echo $_GET["dist"];else {echo "10" ;}} ?>" <?php if(isset($_POST["submit"])){ if(isset($_POST["NearBySearch"])){echo '';} else echo 'disabled'; }else{ if(isset($_GET["NearBySearch"]))echo '';else echo 'disabled'; } ?>><b>miles from</b>

                   <!-- <input type="text" name="dist" value="10"> <main></main>iles from -->
                </div>
                <div style="float: left; margin: 0; padding: 0;">
                  <input id="rad1" type="radio" name="loc" value="curr"  onchange=" handleRadioChnageHere(event)" 
                  <?php if(isset($_POST["submit"])){ if(isset($_POST["loc"]) and (isset($_POST["NearBySearch"]) and $_POST["loc"]=="given" )){  echo '';}else echo 'checked';}else{ if(isset($_GET["loc"]) and (isset($_GET["NearBySearch"]) and $_GET["loc"]=="given" )){echo '';}else echo 'checked';} ?> <?php if(isset($_POST["submit"])){ if(isset($_POST["NearBySearch"])){echo '';} else echo 'disabled'; }else{ if(isset($_GET["NearBySearch"]))echo '';else echo 'disabled'; } ?>> Here<br>
                    <input id="rad2" type="radio"  name="loc" value="given" onchange="handleRadioChange(event)"  <?php if(isset($_POST["submit"])){ if(isset($_POST["loc"]) and (isset($_POST["NearBySearch"]) and $_POST["loc"]=="given" )){  echo 'checked';}else echo '';}else{ if(isset($_GET["loc"]) and (isset($_GET["NearBySearch"]) and $_GET["loc"]=="given" )){echo 'checked';}else echo '';}?> <?php if(isset($_POST["submit"])){ if(isset($_POST["NearBySearch"])){echo '';} else echo 'disabled'; }else{ if(isset($_GET["NearBySearch"]))echo '';else echo 'disabled'; } ?>>
                    <input  id="zip1" type="text" title="zip code" placeholder="zip code" name="givenZip" <?php if(isset($_POST["submit"])){if((isset($_POST["NearBySearch"]) and (isset($_POST["loc"]) and $_POST["loc"]=="given" ))) echo '';else echo 'disabled';}else{if((isset($_POST["NearBySearch"]) and (isset($_GET["loc"]) and $_GET["loc"]=="given" ))) echo '';else echo 'disabled';}  ?> required="true" value= "<?php if(isset($_POST["submit"])){ if(isset($_POST["givenZip"])){echo $_POST["givenZip"];} }else{ if(isset($_GET["givenZip"]))echo $_GET["givenZip"];}?>">
                </div>
                <br style="clear: both;">
                <input type="hidden" id="hereZip" name="hereZip" value="">

                <div style="width: 150px; margin: auto;">
                    <input class="no-margin" type="submit" name="submit" id="sub" disabled value="Search">
                    <input type="button" class="no-margin" onclick="onClear()" value="Clear">
                </div>
            </div>
        </form>
    </div>
    <script>
       function handleChange(event){


        if(event.target.checked == true){


          document.getElementById("dist1").disabled = false;
          document.getElementById("rad1").disabled = false;
          document.getElementById("rad2").disabled = false;
      }else{
          document.getElementById("dist1").disabled = true;
          document.getElementById("rad1").disabled = true;
          document.getElementById("rad2").disabled = true ;

      }


  }

function handleRadioChnageHere(event){

   if(event.target.checked == true){


    document.getElementById("zip1").disabled = true;

    }

  }

  function handleRadioChange(event){

   if(event.target.checked == true){


    document.getElementById("zip1").disabled = false;

}

}

var jsonDoc;
if(typeof myTable != 'undefined'){
  document.body.appendChild(myTable);

  if(document.getElementById("failPara1")){
     var kk = document.getElementById("failPara1");
     kk.parentNode.removeChild(kk);
  }
}
else{

    if(typeof itemDetail!= 'undefined'){
      document.body.appendChild(itemDetail);
        if(itemDetailFail== true){
         if(document.getElementById("idItemTable")){
            document.getElementById("idItemTable").deleteCaption();
         } 

         document.body.appendChild(failurePara); // check;
         itemDetailFail = false;

     }
  
    if(nextDiv)
      document.body.appendChild(nextDiv);
      document.body.style.textAlign= "Center";
      
      var imgButton = document.getElementById("strech");
        if(imgButton!=null)
          imgButton.addEventListener("click", function(){
            var x = this.getAttribute("src");
            var msgLab = document.getElementById("msgLabel1");
            if(msgLab.innerText == "Click here to show Seller Message"){
              msgLab.innerText= "Click here to hide Seller Message";
             }else{
              msgLab.innerText= "Click here to show Seller Message";
              }
            if(x=== "http://csci571.com/hw/hw6/images/arrow_down.png"){
              this.setAttribute("src" , "http://csci571.com/hw/hw6/images/arrow_up.png");
              var outDivObj = document.getElementById("idOutDiv");
              if(outDivObj){

                   if(outDivObj.style.display=="block")
                      outDivObj.style.display="none";

                    var imgObj2 = document.getElementById("strech2");
                    if(imgObj2){
                      if(imgObj2.getAttribute("src")== "http://csci571.com/hw/hw6/images/arrow_up.png");
                        imgObj2.setAttribute("src", "http://csci571.com/hw/hw6/images/arrow_down.png");
                    }
                    var msgLabObj = document.getElementById("msgLabel2");
                    if(msgLabObj){
                      if(msgLabObj.innerText== "Click here to hide Similar Items")
                        msgLabObj.innerText="Click here to show Similar Items";
                    }
                    
              }

        }
        else
            this.setAttribute("src" , "http://csci571.com/hw/hw6/images/arrow_down.png");
        
        this.classList.toggle("active");
        
          var content = this.nextElementSibling.children[0];
         if (content.style.display === "block") {
             content.style.display = "none";
         } else {
             content.style.display = "block";
             if(content.contentWindow)
              content.height = setIframeObj(content.id);
              content.setAttribute("scrolling", "no");
         }


   });

      var imgButton2 = document.getElementById("strech2");

      if(imgButton2!=null)
          imgButton2.addEventListener("click", function(){


            var x = this.getAttribute("src");

            var msgLab = document.getElementById("msgLabel2");
            if(msgLab.innerText == "Click here to show Similar Items"){
              msgLab.innerText= "Click here to hide Similar Items";
          }else{
              msgLab.innerText= "Click here to show Similar Items";
          }



          if(x=== "http://csci571.com/hw/hw6/images/arrow_down.png"){
              this.setAttribute("src" , "http://csci571.com/hw/hw6/images/arrow_up.png");
              var iframeObjj = document.getElementById("idiframeObj");
              if(iframeObjj){

                   if(iframeObjj.style.display=="block")
                      iframeObjj.style.display="none";

                    var imgObj2 = document.getElementById("strech");
                    if(imgObj2){
                      if(imgObj2.getAttribute("src")== "http://csci571.com/hw/hw6/images/arrow_up.png");
                        imgObj2.setAttribute("src", "http://csci571.com/hw/hw6/images/arrow_down.png");
                    }
                    var msgLabObj = document.getElementById("msgLabel1");
                    if(msgLabObj){
                      if(msgLabObj.innerText== "Click here to hide Seller Message")
                        msgLabObj.innerText="Click here to show Seller Message";
                    }
                    
              }

          }
          else
              this.setAttribute("src" , "http://csci571.com/hw/hw6/images/arrow_down.png");
          
          this.classList.toggle("active");
          var content = this.nextElementSibling;
          if (content.style.display === "block") {
             content.style.display = "none";
          } else {
             content.style.display = "block";
          }


     });

    }

}      


function onClear(){
  var tab = document.getElementById("idMyTable");
  if(tab)
        tab.parentNode.removeChild(tab);
    
    var errDivision = document.getElementById("errorDiv");
    if(errDivision)
        errDivision.parentNode.removeChild(errDivision);

  
  if(document.getElementById("catSelect"))    
   document.getElementById("catSelect").selectedIndex = "0"; 

  var chh1 = document.getElementById("ch1");
  if(chh1) {  
    if(chh1.checked) 
      chh1.checked = false;
   } 

  var chh2 = document.getElementById("ch2");
  if(chh2) {  
    if(chh2.checked) 
      chh2.checked = false;
   }

   var chh3 = document.getElementById("ch3");
    if(chh3) {  
      if(chh3.checked) 
        chh3.checked = false;
     }

  var shh1 = document.getElementById("sh1");
    if(shh1) {  
      if(shh1.checked) 
        shh1.checked = false;
     }   

    var shh2 = document.getElementById("sh2");
    if(shh2) {  
      if(shh2.checked) 
        shh2.checked = false;
     } 


    var shh3 = document.getElementById("sh3");
    if(shh3) {  
      if(shh3.checked) {
        shh3.checked = false;
        document.getElementById("dist1").value="10";  
        document.getElementById("dist1").disabled = true;
        document.getElementById("rad1").disabled = true;
        document.getElementById("rad1").checked = true;
        document.getElementById("zip1").disabled= true;

        document.getElementById("rad2").disabled = true ;
      }
     }  






    var x = <?php  clear();

    ?>

    postJSON="";
    getJSON="";

       
       document.getElementById("hey").value="";
       document.getElementById("zip1").value="";


      
       var itemtab = document.getElementById("idItemTable");
       if(itemtab)
       itemtab.parentNode.removeChild(itemtab);
       var nextDivObj = document.getElementById("idNextDiv");
       if(nextDivObj)
       nextDivObj.parentNode.removeChild(nextDivObj);
       var failParaDel = document.getElementById("failPara1");
       if(failParaDel)
       failParaDel.parentNode.removeChild(failParaDel);

   }

   function loadJSON (url) {

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("GET", url ,false); 
        xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 ) {
        if (xmlhttp.status == "200")
        jsonDoc=xmlhttp.responseText;
     else{
      jsonDoc="";
      
     }
    }
  };
   xmlhttp.send(); 
   
   return jsonDoc;



    }

    function getLocation(){

      jsonDoc=loadJSON("http://ip-api.com/json");
        // handle enable and disable of the button @pankhuri
        if(jsonDoc!=""){
          document.getElementById("sub").disabled=false;
          locJSONObj =  JSON.parse (jsonDoc);
          document.getElementById("hereZip").value = locJSONObj.zip;
        }

    }


    if(typeof itemDetailFail != 'undefined'){

       

}

function setIframeObj(id){
  var ifrmm = document.getElementById(id);
  var doc = ifrmm.contentDocument? ifrmm.contentDocument: 
  ifrmm.contentWindow.document;
 

return getHeight( doc )+5+"px";

}

function getHeight(doc) {
  doc = doc || document;
   
    var body = doc.body, html = doc.documentElement;
    var height = Math.max( body.scrollHeight, body.offsetHeight, 
        html.clientHeight, html.scrollHeight, html.offsetHeight );
    return height;
  
}
</script>  
</body>  
</Html>
<html lang="ru">
<head>
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
</head>
<body class="container">
<h1>Feedback</h1>
<?

include("crest.php");

$message = '';
if(!empty($_POST['SAVE']))
{

   $client_id = '';

   $client_id = CRest::call(
      'crm.contact.list',
      [
         'filter' => ['ASSIGNED_BY_ID' => 6],
         'select' => ['ID']
      ]
   );

   if(!empty($client_id['result'][0]['ID']) && !is_null($client_id['result'][0]['ID'])){

      $deal_id = '';
      $deal_id = CRest::call(
         'crm.deal.list',
         [
            'filter' => ['ASSIGNED_BY_ID' => 1], // ID ЗАКАЗА
            'select' => ['ID']
         ]
      );

      if(!empty($deal_id['result'][0]['ID']) && !is_null($deal_id['result'][0]['ID'])){
         $dealFields = [
            'TITLE' => 'Сделка: '.$_POST['NAME'].' '.$_POST['LAST_NAME'],
            'CONTACT_ID' => $resultContact['result'],
            'CURRENCY_ID' => 'BYN',
            'COMMENTS' => $_POST['COMMENTS'],
            'ASSIGNED_BY_ID' => 1,
            'UF_CRM_1626018533' => 'Самовывоз',
            'UF_CRM_1626018579' => 'P.Zadnipru',
            'UF_CRM_1626018630' => '1',
            'UF_CRM_1626018683' => $_POST['DELIVERY_DATE'],
            'UF_CRM_1626018806' => 'online',
            'UF_CRM_1626018848' => 'new promo',
            'OPPORTUNITY' => 100.0,
         ];

         $resultDeal = CRest::call(
            'crm.deal.update',
            [
               'id' => $deal_id['result'][0]['ID'],
               'fields' => $dealFields
            ]
         );

         $arDealContactFields = [
            'CONTACT_ID' => $client_id['result'][0]['ID']
         ];
   
         $resultDealContact = CRest::call(
            'crm.deal.contact.add',
            [
               'id' => $resultDeal['result'],
               'fields' => $arDealContactFields
            ]
         );

      } else {
         $arDealFields = [
            'TITLE' => 'Сделка: '.$_POST['NAME'].' '.$_POST['LAST_NAME'],
            'CONTACT_ID' => $resultContact['result'],
            'CURRENCY_ID' => 'BYN',
            'COMMENTS' => $_POST['COMMENTS'],
            'ASSIGNED_BY_ID' => 1,
            'UF_CRM_1626018533' => 'Самовывоз',
            'UF_CRM_1626018579' => 'P.Zadnipru',
            'UF_CRM_1626018630' => '1',
            'UF_CRM_1626018683' => $_POST['DELIVERY_DATE'],
            'UF_CRM_1626018806' => 'online',
            'UF_CRM_1626018848' => 'new promo',
            'OPPORTUNITY' => 100.0,
         ];
   
         $resultDeal = CRest::call(
            'crm.deal.add',
            [
               'fields' => $arDealFields
            ]
         );

         $arDealContactFields = [
            'CONTACT_ID' => $client_id['result'][0]['ID']
         ];
   
         $resultDealContact = CRest::call(
            'crm.deal.contact.add',
            [
               'id' => $resultDeal['result'],
               'fields' => $arDealContactFields
            ]
         );
      }

   } else {
      $fields = [
         'NAME' => $_POST['NAME'],
         'LAST_NAME' => $_POST['LAST_NAME'],
         'PHONE' => [ ["VALUE"=>$_POST['PHONE'] ] ],
         'EMAIL' => [ ["VALUE"=>$_POST['EMAIL'] ] ],
         'BIRTHDATE' => $_POST['BIRTHDATE'],
         'COMMENTS' => $_POST['COMMENTS'],
         'ASSIGNED_BY_ID' => 6
      ];
   
      $resultContact = CRest::call(
         'crm.contact.add',
         [
            'fields' => $fields
         ]
      );

      $arDealFields = [
         'TITLE' => 'Сделка: '.$_POST['NAME'].' '.$_POST['LAST_NAME'],
         'CONTACT_ID' => $resultContact['result'],
         'CURRENCY_ID' => 'BYN',
         'COMMENTS' => $_POST['COMMENTS'],
         'ASSIGNED_BY_ID' => 1,
         'UF_CRM_1626018533' => 'Самовывоз',
         'UF_CRM_1626018579' => 'P.Zadnipru',
         'UF_CRM_1626018630' => '1',
         'UF_CRM_1626018683' => $_POST['DELIVERY_DATE'],
         'UF_CRM_1626018806' => 'online',
         'UF_CRM_1626018848' => 'new promo',
         'OPPORTUNITY' => 100.0,
      ];

      $resultDeal = CRest::call(
         'crm.deal.add',
         [
            'fields' => $arDealFields
         ]
      );
   }

   if (!empty($resultContact['result']))
   {

      if(!empty($resultDeal['result'])){

         $arProductFields = [
            'PRICE' => 100.50,
            'CURRENCY_ID' => 'BYN',
            'NAME' => 'Товар 1',
            'ACTIVE' => 'Y',
            'MEASURE' => 1,
            'PREVIEW_PICTURE' => 'none',
            'DESCRIPTION' => 'какое-то описание',
            'CATALOG_ID' => $resultDeal['result'],
         ];
   
         $resultProduct = CRest::call(
            'crm.product.add',
            [
               'fields' => $arProductFields
            ]
         );

         if(!empty($resultProduct['result'])){

            $arProductRowsFields = [ 
               [
                  'PRODUCT_ID' => $resultProduct['result'],
                  'PRICE' => 100.0,
                  'QUANTITY' => 4
               ] 
            ];
      
            $resultProductRows =  CRest::call(
               'crm.deal.productrows.set',
               [
                  'id' => $resultDeal['result'],
                  'rows' => $arProductRowsFields
               ]
            );
         }

      }

      if (!empty($resultDeal['result']))
      {
         if(!empty($_POST['TRACE']))
         {

            $resultTrace = CRest::call(
               'crm.tracking.trace.add',
               [
                  'ENTITIES' => [
                     [
                        'TYPE' => 'CONTACT',//COMPANY, CONTACT, DEAL, LEAD, QUOTE
                        'ID' => $resultContact['result']
                     ],
                     [
                        'TYPE' => 'DEAL',//COMPANY, CONTACT, DEAL, LEAD, QUOTE
                        'ID' => $resultDeal['result']
                     ]
                  ],
                  'TRACE' =>  $_POST['TRACE']
               ]
            );

         }

         $message = 'Feedback saved';
      }
      elseif (!empty($resultDeal['error_description']))
      {
         $message =  'Feedback has not been saved: '.$resultDeal['error_description'];
      }
      else
      {
         $message = 'Feedback has not been saved';
      }
   }
   elseif (!empty($resultContact['error_description']))
   {
      $message =  'Feedback has not been saved: '. $resultContact['error_description'];
   }
   else
   {
      $message = 'Feedback has not been saved';
   }
}
?>
<div class="col-12">
   <?=$message?>
   <?print_r($resultTrace)?>
</div>
<form method="post" action="">
   <input type="hidden" id="FORM_TRACE" name="TRACE">

   <br>

   <h1>Контакт</h1>

   <div class="row">
      <div class="col-4 mt-3">
         <label>Имя*</label>
      </div>
      <div class="col-6 mt-3">
         <input type="text" name="NAME" required>
      </div>
   </div>
   <div class="row">
      <div class="col-4 mt-3">
         <label>Фамилия*</label>
      </div>
      <div class="col-6 mt-3">
         <input type="text" name="LAST_NAME" required>
      </div>
   </div>
   <div class="row">
      <div class="col-4 mt-3">
         <label>Отчество*</label>
      </div>
      <div class="col-6 mt-3">
         <input type="text" name="SECOND_NAME" required>
      </div>
   </div>
   <div class="row">
      <div class="col-4 mt-3">
         <label>Номер*</label>
      </div>
      <div class="col-6 mt-3">
         <input type="text" name="PHONE" required>
      </div>
   </div>
   <div class="row">
      <div class="col-4 mt-3">
         <label>Email*</label>
      </div>
      <div class="col-6 mt-3">
         <input type="text" name="EMAIL" required>
      </div>
   </div>
   <div class="row">
      <div class="col-4 mt-3">
         <label>Дата рождения*</label>
      </div>
      <div class="col-6 mt-3">
         <input type="date" name="BIRTHDATE" required>
      </div>
   </div>
   <div class="row">
      <div class="col-4 mt-3">
         <label>Комментарий*</label>
      </div>
      <div class="col-6 mt-3">
         <textarea name="COMMENTS" required></textarea>
      </div>
   </div>

   <br>

   <h1>Сделка</h1>

   <div class="row">
      <div class="col-4 mt-3">
         <label>Дата доставки*</label>
      </div>
      <div class="col-6 mt-3">
         <input type="datetime-local" name="DELIVERY_DATE" required>
      </div>
   </div>

   <div class="row">
      <div class="col-sm-10">
         <input type="submit" name="SAVE" class="btn btn-primary" value="Send">
      </div>
   </div>

</form>
<script>
   window.onload = function(e){
      var traceDom = document.getElementById('FORM_TRACE');
      if(traceDom)
      {
         var trace = b24Tracker.guest.getTrace();
         traceDom.value = trace;
      }
   }
</script>
</body>
</html>
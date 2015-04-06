<form action="{$purl}" method="post" name="ePayment" id="ipay88form">	
	<a href="#" id="iPay88SubmitBtn"><img src="{$logoURL}" alt="logo" width="200" height="60" /></a>
	<input type="hidden" name="MerchantCode" value="{$mcode}" />
	<input type="hidden" name="PaymentId" value="1" /> {*PAYMENT ID: 1 = CREDIT CARD PH, 5 = BANCNET*}
	<input type="hidden" name="RefNo" value="{$refNo}" />
	<input type="hidden" name="Amount" value="{$amount}" />
	<input type="hidden" name="Currency" value="{$currency}" />
	<input type="hidden" name="ProdDesc" value="PlanetSportProduct" />
	<input type="hidden" name="UserName" value="{$customer}" />
	<input type="hidden" name="UserEmail" value="{$email}" />
	<input type="hidden" name="UserContact" value="{$tel}" />
	<input type="hidden" name="Remark" value="" />
	<input type="hidden" name="Lang" value="UTF-8"/>
	<input type="hidden" name="Signature" value="{$signature}" />
	<input type="hidden" name="ResponseURL" value="{$responseURL}" />
	<input type="hidden" name="BackendURL" value="{$backendPostURL}"/>
</form>
<script type="text/javascript">
	{literal}
		$('#iPay88SubmitBtn').on('click',function(e){
			e.preventDefault();
			$('#ipay88form').submit();
			return false;
		});
	{/literal}
</script>
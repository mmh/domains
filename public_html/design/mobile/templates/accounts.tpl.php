<form action="#" method="post" accept-charset="utf-8" id="accountsToDomains">

<div class="help">Shows only domains with no owner assigned and only the servername defined in the vhost</div>

<div class="ui-widget">
	<label for="account">Account name: </label>
    <input type="text" name="account_name" value="" id="accountName"/>
    <input type="hidden" name="account_id" value="" id="accountID"/>
    
    <br />

    <label for="domains">Domains</label>
    <select name="domains[]" id="domains" size="20" multiple="multiple">
<?php
foreach ($domains as $domain) 
{
      echo '<option value="'.$domain->id.'">'.$domain->name.'</option>';
}
?>
    </select>

    <br />

    <input type="submit" name="actionSubmit" value="Tilføj" id="submit"/>   

</div>
</form>

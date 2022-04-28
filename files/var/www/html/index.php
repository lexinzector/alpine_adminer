<html>
<head>
</head>
<body>

<div>Redirect. Please wait...</div>
<form id="adminer_form" method="post" action="./adminer-4.8.1.php">
	<input type='hidden' name='auth[driver]' value='server' />
	<input type='hidden' name='auth[server]' value='<?= getenv("MYSQL_HOST") ?>' />
	<input type='hidden' name='auth[username]' value='<?= getenv("MYSQL_LOGIN") ?>' />
	<input type='hidden' name='auth[password]' value='<?= getenv("MYSQL_PASSWORD") ?>' />
	<input type='hidden' name='auth[permanent]' value='1' />
</form>

<script>
    adminer_form.submit();
</script>

</body>
</html>
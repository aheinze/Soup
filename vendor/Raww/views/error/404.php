<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Page Not Found</title>
<style type="text/css">
body {
    font-family: Helvetica, Arial, sans-serif;
    font-size: 18px;
    font-style: normal;
    font-weight: normal;
    text-transform: normal;
    letter-spacing: normal;
    line-height: 1.4em;;
    color:black;
    background-color:#eee;
}
h1 { font-size:44px;letter-spacing: -2px;line-height: 1.2em; }
h2 { font-weight:normal;color:maroon }
h3 { font-weight:bold;font-size:11pt}
p  { margin-top: 5px 0px;}
.wrapper{
    width:640px;
    margin:40px auto;
    padding:20px;
    background-color:white;
    border: 1px #ccc solid;
    -moz-box-shadow:0px 0px 5px #ccc;
    -webkit-box-shadow:0px 0px 10px #ccc;
    box-shadow:0px 0px 5px #ccc;
}
.version {
    margin-top: 30px;
    padding-top: 10px;
    color: gray;font-size:8pt;
    border-top:1px #ccc dotted;
}
</style>
</head>
<body>
    <div class="wrapper">
        <h1>Page Not Found</h1>
        <h2><?php echo isset($message) ? nl2br($message) : ""; ?></h2>
        <p>
            The requested URL was not found on this server.
            If you entered the URL manually please check your spelling and try again.
        </p>
        <p>
            If you think this is a server error, please contact the admin.
        </p>
        <div class="version">
            System message generated by Raww2
        </div>
    </div>
</body>
</html>
<?php
session_start();
if (isset($_SESSION['nickName']) && !empty($_SESSION['nickName'])) {
    $person = $_SESSION['nickName'];
}
if (isset($_POST['replyText']) && !empty($_POST['replyText']) && !empty($_SESSION['nickName'])) {
    $reply = $_POST['replyText'];
    $text = "$person : $reply";
    $fileResource = fopen("chatHistory.txt", "a+");
    fwrite($fileResource, "$text\n");
    fclose($fileResource);
} ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Chat On</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/chat.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
</head>
<body>
<?php
if (!isset($_POST['nickName']) && empty($_SESSION['nickName'])) {
    ?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <h2>Welcome to Private chat</h2>
        <input type="text" name="nickName" placeholder="User Name">
        <input type="submit" value="Enter" id="send">
    </form>

<?php } elseif (isset($_POST['nickName'])) {

    $person = $_POST['nickName'];
    if (empty($person)) {
        ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <?php
            echo "<h2>Welcome to Private chat</h2>";
            echo "<p class=\"error\">User Name can not be empty</p>";
            ?>
            <input type="text" name="nickName" placeholder="User Name">
            <input type="submit" value="submit" id="send">
        </form>
        <?php
    } else {
        $_SESSION['nickName'] = $person;
    }
}
if (!isset($_POST['destroyChat']) && !isset($_POST['logOut']) && isset($_SESSION['nickName']) && !empty($_SESSION['nickName'])) {
    ?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="window">
        <?php echo "<h3><span></span>$person's window</h3>"; ?>
        <div id="notify"></div>
        <textarea rows="10" placeholder="No Conversation to show" id="display" disabled readonly><?php
            $fileSize = filesize("chatHistory.txt");
            if ($fileSize > 0) {
                $fileResource = fopen("chatHistory.txt", "r+");
                $chat = fread($fileResource, filesize("chatHistory.txt"));
                echo $chat;
            }
            ?></textarea>
        <input type="text" name="replyText" class="replyText" placeholder="Type your reply">
        <input type="submit" value="►" class="Reply">
        <input type="submit" name="logOut" value="Log Off" class="danger">
        <input type="submit" name="destroyChat" value="Clear History" class="danger">
    </form>
<?php }
if (isset($_POST['logOut']) && !empty($_SESSION['nickName'])) {
    unset($_SESSION['nickName']);
    header("location: chat.php");
}
if (isset($_POST['destroyChat']) && !empty($_SESSION['nickName'])) {
    file_put_contents("chatHistory.txt", "");
    header("location: chat.php");
}
?>

</body>
<script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>

<script>
    var fileBefore = "";
    var textarea = document.getElementById("display");
    textarea.scrollTop = textarea.scrollHeight;
    $(document).ready(function () {
        setInterval(function () {
            var ajax = new XMLHttpRequest();
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {
                    if (ajax.responseText != fileBefore) {
                        fileBefore = ajax.responseText;
                        $('#display').load("chatHistory.txt");
                        if (ajax.readyState == +4 && textarea.scrollTop + 237 < textarea.scrollHeight) {
                            $('#notify').html("new message").fadeIn('fast').delay(1500).fadeOut('slow');
                        }
                    }
                }
            };
            ajax.open("POST", "chatHistory.txt", true); //Use POST to avoid caching
            ajax.send();
        }, 1000);
    });
</script>
</html>
<?php
$errors = [];
$missing = [];
// Check if form is submitted
if (isset($_POST['send'])) {
    $expected = ['name', 'email', 'message', 'gender', 'terms'];
    $required = ['name', 'message', 'gender', 'terms'];
    $to = "Jaimal Dullat <jaimal@example.com>";
    $subject = "Feedback from online form";
    $headers = [];
    $headers[] = "From: user@example.com";
    $headers[] = "Cc: anotheradmin@example.com";
    $headers[] = "Content-type: text/plain; charset=utf-8";
    $authorized = null;
    if (!isset($_POST['gender'])) {
        $_POST['gender'] = '';
    }
    if (!isset($_POST['terms'])) {
        $_POST['terms'] = '';
    }
    // Include separate file process_email.php
    include "./includes/process_mail.php";
    if ($mailSent) {
        header('Location: thanks.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Feedback Form | PHP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css">
</head>
<body>
<div class="feedback-form">
    <h2>Feedback Form</h2>
    <?php if ($_POST && ($suspect || isset($errors["mailfail"]))): ?>
        <p class="warning">Sorry, your email couldn't be sent</p>
    <?php endif; ?>
    <?php if ($errors || $missing): ?>
        <p class="warning">Please fix the item(s) indicated</p>
    <?php endif; ?>
    <form class="form" method="POST" action="<?= $_SERVER['PHP_SELF']; ?>">
        <div class="form-control">
            <label>
                Name:<span class="warning">*</span>
                <?php if ($missing && in_array('name', $missing)): ?>
                    <span class="warning">Please enter your name</span>
                <?php endif; ?>
            </label>
            <input type="text" name="name"
                <?php if ($missing || $errors) {
                    echo 'value="' . htmlentities($name) . '"';
                }
                ?>
            >
        </div>
        <div class="form-control">
            <label>
                Email:
                <?php if ($missing && in_array('email', $missing)): ?>
                    <span class="warning">Please enter your email</span>
                <?php elseif (isset($errors["email"])): ?>
                    <span class="warning">Invalid email address</span>
                <?php endif; ?>
            </label>
            <input type="text" name="email"
                <?php if ($missing || $errors):
                    echo 'value="' . htmlentities($email) . '"';
                endif;
                ?>
            >
        </div>
        <div class="form-control">
            <label>
                Message:<span class="warning">*</span>
                <!-- /.warning -->
                <?php if ($missing && in_array('message', $missing)): ?>
                    <span class="warning">You forgot to add any message</span>
                <?php endif; ?>
            </label>
            <textarea name="message" rows="10"><?php if ($missing || $errors):
                    echo htmlentities($message);
                endif;
                ?></textarea>
        </div>
        <div class="form-control">
            <fieldset>
                <legend>Gender:<span class="warning">*</span>
                    <?php if ($missing && in_array('gender', $missing)): ?>
                        <span class="warning">Select gender</span>
                    <?php endif; ?>
                </legend>
                <p>
                    <input type="radio" name="gender" value="male" id="gender_m"
                        <?php if ($_POST && $gender == 'male')
                            echo 'checked';
                        ?>
                    >
                    <label for="gender_m">Male</label>
                    <input type="radio" name="gender" value="female" id="gender_f"
                        <?php if ($_POST && $gender == 'female'):
                            echo 'checked';
                        endif;
                        ?>
                    >
                    <label for="gender_f">Female</label>
                </p>
            </fieldset>
        </div>
        <!-- /.form-control -->
        <div class="form-control">
            <?php if ($missing && in_array('terms', $missing)): ?>
                <span class="warning">Please accept terms and conditions</span>
            <?php endif; ?>
            <label>
                <input type="checkbox" name="terms" value="agreed"
                    <?php if ($_POST && $terms == 'agreed'):
                        echo "checked";
                    endif;
                    ?>
                >
                I agree to the terms and conditions
                <span class="warning">*</span>
            </label>
        </div>
        <!-- /.form-control -->
        <p class="warning">* = Required fields</p>
        <div class="form-control">
            <button type="submit" name="send">Send Feedback</button>
        </div>
    </form>
    <pre>
        <?php
        if ($_POST && $mailSent) {
            echo "Message: \n\n";
            echo htmlentities($emailMessage);
            echo "Headers: \n\n";
            echo htmlentities($headers);
        }
        ?>
    </pre>
</div>
<!-- /.feedback-form -->
</body>
</html>
<?php
// Check email is ent or not
$mailSent = false;
// Assume input contains no suspect phrase
$suspect = false;
// Regular expression to search for suspect phrase
$pattern = '/Content-type:|Cc:|Bcc:/i';
// Recursive function to check any suspect
// Third argument is passed by reference using &
function isSuspect($value, $pattern, &$suspect)
{
    if (is_array($value)) {
        foreach ($value as $item) {
            isSuspect($item, $pattern, $suspect);
        }
    } else {
        if (preg_match($pattern, $value)) {
            $suspect = true;
        }
    }
}

// Check $_POST array for any suspect phrase
isSuspect($_POST, $pattern, $suspect);
// Process form if no suspect phrase found
if (!$suspect):
    // Check required fields are filled
    // Reassign values to simple variables
    foreach ($_POST as $key => $value) {
        $value = is_array($value) ? $value : trim($value);
        if (empty($value) && in_array($key, $required)) {
            $missing[] = $key;
            $$key = '';
        } elseif (in_array($key, $expected)) {
            $$key = $value;
        }
    }
    // Verify user's email address is correct
    if (!$missing && !empty($email)):
        $validemail = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if ($validemail) {
            $headers[] = "Reply-to: $validemail";
        } else {
            $errors['email'] = true;
        }
    endif;
    // If no error contains message body
    if (!$errors && !$missing):
        $headers = implode("\r\n", $headers);
        // Initialize message
        $emailMessage = '';
        foreach ($expected as $field):
            if (isset($$field) && !empty($$field)) {
                $val = $$field;
            } else {
                $val = "Not selected";
            }
            // Check if val an array , expand to a comma separated string
            if (is_array($val)) {
                $val = implode(', ', $val);
            }
            // Replace underscores in the field name with spaces
            $field = str_replace('_', ' ', $field);

            $emailMessage .= ucfirst($field) . ": $val\r\n\r\n";
        endforeach;
        $emailMessage = wordwrap($emailMessage, 75);
        $mailSent = true;
        //$mailSent = mail($to, $subject, $emailMessage, $headers, $authorized);
        if(!$mailSent){
            $errors['mailfail'] = true;
        }
    endif;
endif;
?>
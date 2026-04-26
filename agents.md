# Leadership award application

- Only one application should be made regardless of the award type. When a user has submitted an application, the button should show Application Sent and becomes filement "warning". When the admin accepts it the button becomes filament "success" Application Accepted. When its rejected, users can send another one.

An email should be automatically sent when accepted or rejected. Please make the email multiline heres a sample on how to make it multiline:

$message = <<<EOT
Greetings!

This is a reminder that you have a pending {$target['type']} evaluation for {$target['evaluatee_name']} in {$evaluationTitle}.

Please submit it as soon as possible.
EOT;

When the award has been accepted, please tell the user to go to the Student Affairs and Academic Services office to claim it.

Accepted:

Greetings {student name}!

We are happy to inform you that your application for the {award type} has been accepted! Please claim your award at the Student Affairs and Academic Services office to claim it.

Sincerely,
Paulinian Student Government

Rejected:
Greetings {student name}!

We regret to inform you that your application for the {award type} has been unsuccessful at this time. You are welcome to reapply once all necessary application requirements have been completed and submitted.

Sincerely,
Paulinian Student Government

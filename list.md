# To Do List

### 1. Submission States of Evaluations

    - Client requested that the submission fo evaluation forms should have states sush as submitted, pending, and draft. When lets say I havent completed my evaluation, I saved it as draft, when I get back the answers I left are there.

### 2. Evaluation Form View - Admin Side

    - The client requested that when viewing a submitted evaluation form as an adviser or admin, beside the back button, there should be a download button that downloads the evaluation form as a PDF.

### 3. Evaluation Results Reccomnedations

    - Lets add infolists that is displayed the same way as the statoverview cards that shows information about reccomendations for certain scores. It should Be dispalyed in the Evaluation View Page. Just create me three infolists or sections and Ill do the contents

### 4. Certficate Feature

    - Certificates will no longer be controlled by students. The adviser or admin will be the one issuing the certificates to avoid fake documents being uploaded. Students can just download them as pdf. Advisers and Admin can select which multiple students that they will issue the certificate too.

### 5. Printable Evaluation Form.

    - The client requested that they can download the evaluation form. They didnt specify which format so can we just se the dowload button when clicked, a drop down will show allowing you to select wether it be downlaoded as a .csv or pdf? The csv file should show all the questions, the available options, the answer selected, the evaluatee name, the evaluator's name, the date submitted.

### Department Resource.

    - The client also requested a department resource thatw works similar to the award type resource. Its just a data for filtering. The fields should be Department Name, Description, Timestamps. We assign departments to users as a required field. When we create a council, we can select what type of students can be attached. For example, I have a council and I choose to only allow users from School of Information Technology and Engineering and School of Nursing and Allied HEalth Sevices to be attached. The reason for this is during the demo, the client saw that there is no constraint for selectin students to councils

## Reminders

    - When creating a new resource, please update the schema, table, and the routing after creating or editing a record similar to how the other resources look and work.
    - Feel free to edit migration files, we can just do I migrate:fresh --seed since this isnt in productions yet.

## Follow ups to the department and council revisions
    - First of all, you did well, thank you. A few minor revisions left. Please hide the actions in the department table, and please update the routing after creating or editing a record in the department where it should redirect back to the list view.
    - I forgot to ask you about the academic year field when creating an evalution. I can input text which is bad. Can you set it as a datepicker? Like the pasted image 2? Actually it doesnt have to be a date picker, it just need to be a consistent numeric only and it should only show the year range like 2022-2023. Its up to you.
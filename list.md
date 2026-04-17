# To Do List

### 1. Award Application Process Improvement

    - Improve the looks of the modal for applying for a leadership award. Match it with the modals in the my evalaution relation table.
    - The cleint requested a decision support. I think this is doable by simply updating the award application tbale to include a field called Rank. This field should display the rank he got from the most recent evaluation of a council with the same award type requested.
    - In the admin view of the portfolio, lets update the award and award type. I like the layout of the award type but we need to remove the description and add rank there too per council evaluations. These should act like helper text.s
    - Lets update the action column to be like a three dot icon that allows the user to view portfolio (info), accept (success), adn reject (danger). The status field should just display Icons with colors corresponding to their state.
    - Update the student name to student infor. The image, name, and department should be displayed similar to the relational table of my evaluation

## Reminders

    - When creating a new resource, please update the schema, table, and the routing after creating or editing a record similar to how the other resources look and work.
    - Feel free to edit migration files, we can just do I migrate:fresh --seed since this isnt in productions yet.

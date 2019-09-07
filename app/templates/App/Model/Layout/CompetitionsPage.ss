<% if $UpcomingCompetitions %>
    <section>
        <h2>Upcoming Competitions</h2>
        <ul>
        <% loop $UpcomingCompetitions %>
            $Name
        <% end_loop %>
        </ul>
    </section>
<% end_if %>

<% if $PastCompetitions %>
    <section>
        <h2>Past Competitions</h2>
        <ul>
        <% loop $PastCompetitions %>
            $Name
        <% end_loop %>
        </ul>
    </section>
<% end_if %>
<% if $Competition %>
    <% with $Competition %>
        <h2>$Name</h2>
        <p>Date: $StartDate.Format('MMMM d') - $EndDate.Format('d, YYYY')</p>
        <p>City: $City</p>
        <p>Venue: $Venue</p>
        <p>Address: $VenueAddress</p>
        <p>Registration: Opens $RegistrationOpen.Format('EEEE, MMMM d, YYYY, h:mm a') and closes $RegistrationClose.Format('EEEE, MMMM d, YYYY, h:mm a.')</p>
        <p><a href="https://www.worldcubeassociation.org/competitions/$WCAID#competition-events">Events</a></p>
        <p><a href="https://www.worldcubeassociation.org/competitions/$WCAID#competition-schedule">Schedule</a></p>
    <% end_with %>
<% end_if %>
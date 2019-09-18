<% if $Competition %>
    <h2>$Competition.Name</h2>
    <% if $Competition.IsRegistrationClosed %>
        <p>Registration closed $Competition.RegistrationClose.Format('EEEE, MMMM d, YYYY, h:mm a.')</p>
    <% else_if $Competition.IsRegistrationOpen %>
        <p>Registration closes $Competition.RegistrationClose.Format('EEEE, MMMM d, YYYY, h:mm a.')</p>
    <% else %>
        <p>Registration opens $Competition.RegistrationOpen.Format('EEEE, MMMM d, YYYY, h:mm a') and closes $Competition.RegistrationClose.Format('EEEE, MMMM d, YYYY, h:mm a.')</p>
    <% end_if %>

    <% if $Competition.IsRegistrationOpen %>
        <% if $CurrentMember %>
            <p>Hi, $CurrentMember.Name!</p>
            <p>If this is not you, please <a href="$absoluteBaseURL\Security/logout">logout</a> and login again.

            $RegistrationForm
<br><br><br>
            $PaymentForm
        <% else %>
            $LoginButton
        <% end_if %>
    <% end_if %>
<% end_if %>
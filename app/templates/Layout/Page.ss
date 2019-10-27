<% include SideBar %>
<div class="content-container unit size3of4 lastUnit">
	<article>
		<h1>$Title</h1>
		<div class="content">
            <% if $ElementalArea %>
                $ElementalArea
            <% else %>
                $Content
            <% end_if %>
        </div>
	</article>
		$Form
		$CommentsForm
</div>
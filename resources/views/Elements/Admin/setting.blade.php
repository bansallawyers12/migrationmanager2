<?php
		if(Auth::check()) {
			$roles = \App\Models\UserRole::find(Auth::user()->role);
			$newarray = json_decode($roles->module_access);
			$module_access = (array) $newarray;
		} else {
			$module_access = [];
		}
?>
<div class="custom_nav_setting">
    <ul>
        <?php
			if(Route::currentRouteName() == 'admin.feature.producttype.index' || Route::currentRouteName() == 'admin.feature.producttype.create' || Route::currentRouteName() == 'admin.feature.producttype.edit' || Route::currentRouteName() == 'admin.feature.visatype.index' || Route::currentRouteName() == 'admin.feature.visatype.create' || Route::currentRouteName() == 'admin.feature.visatype.edit' || Route::currentRouteName() == 'admin.feature.source.index' || Route::currentRouteName() == 'admin.feature.source.create' || Route::currentRouteName() == 'admin.feature.source.edit' || Route::currentRouteName() == 'admin.feature.tags.index' || Route::currentRouteName() == 'admin.feature.tags.create' || Route::currentRouteName() == 'admin.feature.tags.edit' || Route::currentRouteName() == 'admin.workflow.index' || Route::currentRouteName() == 'admin.workflow.create' || Route::currentRouteName() == 'admin.workflow.edit'){
				$addfeatureclasstype = 'active';
		}
		?>
		{{--
		<li class="{{(Route::currentRouteName() == 'admin.feature.profiles.index' || Route::currentRouteName() == 'admin.feature.profiles.create' || Route::currentRouteName() == 'admin.feature.profiles.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.feature.profiles.index')}}">Profiles</a></li>
		<li class="{{(Route::currentRouteName() == 'admin.feature.producttype.index' || Route::currentRouteName() == 'admin.feature.producttype.create' || Route::currentRouteName() == 'admin.feature.producttype.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.feature.producttype.index')}}">Product Type</a></li>
		<li class="{{(Route::currentRouteName() == 'admin.feature.mastercategory.index' || Route::currentRouteName() == 'admin.feature.mastercategory.create' || Route::currentRouteName() == 'admin.feature.mastercategory.edit' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.feature.mastercategory.index')}}">Master Category</a></li>
    	<li class="{{(Route::currentRouteName() == 'admin.feature.visatype.index' || Route::currentRouteName() == 'admin.feature.visatype.create' || Route::currentRouteName() == 'admin.feature.visatype.edit' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.feature.visatype.index')}}">Visa Type</a></li>
		<li class="{{(Route::currentRouteName() == 'admin.feature.source.index' || Route::currentRouteName() == 'admin.feature.source.create' || Route::currentRouteName() == 'admin.feature.source.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.feature.source.index')}}">Source</a></li>
		--}}
		<li class="{{(Route::currentRouteName() == 'admin.feature.tags.index' || Route::currentRouteName() == 'admin.feature.tags.create' || Route::currentRouteName() == 'admin.feature.tags.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.feature.tags.index')}}">Tags</a></li>
		{{-- Commented out routes that don't exist yet
		<li class="{{(Route::currentRouteName() == 'admin.checklist.index' || Route::currentRouteName() == 'admin.checklist.create' || Route::currentRouteName() == 'admin.checklist.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.checklist.index')}}">Checklist</a></li>
		<li class="{{(Route::currentRouteName() == 'admin.enquirysource.index' || Route::currentRouteName() == 'admin.enquirysource.create' || Route::currentRouteName() == 'admin.enquirysource.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.enquirysource.index')}}">Enquiry Source</a></li>
		--}}

        <li class="{{(Route::currentRouteName() == 'admin.workflow.index' || Route::currentRouteName() == 'admin.workflow.create' || Route::currentRouteName() == 'admin.workflow.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.workflow.index')}}">Workflow</a></li>

        <li class="{{(Route::currentRouteName() == 'admin.emails.index' || Route::currentRouteName() == 'admin.emails.create' || Route::currentRouteName() == 'admin.emails.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.emails.index')}}">Email</a></li>
		<li class="{{(Route::currentRouteName() == 'admin.crmemailtemplate.index' || Route::currentRouteName() == 'admin.crmemailtemplate.create' || Route::currentRouteName() == 'admin.crmemailtemplate.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.crmemailtemplate.index')}}">Crm Email Template</a></li>

		<?php
			if(Route::currentRouteName() == 'admin.branch.index' || Route::currentRouteName() == 'admin.branch.create' || Route::currentRouteName() == 'admin.branch.edit' || Route::currentRouteName() == 'admin.branch.userview' || Route::currentRouteName() == 'admin.branch.clientview' || Route::currentRouteName() == 'admin.users.active' || Route::currentRouteName() == 'admin.users.inactive' || Route::currentRouteName() == 'admin.users.invited' || Route::currentRouteName() == 'admin.userrole.index' || Route::currentRouteName() == 'admin.userrole.create' || Route::currentRouteName() == 'admin.userrole.edit'){
				$teamclasstype = 'active';
			}
		?>
			<?php
			if(array_key_exists('1',  $module_access)) {
			?>
			<li class="{{(Route::currentRouteName() == 'admin.branch.index' || Route::currentRouteName() == 'admin.branch.create' || Route::currentRouteName() == 'admin.branch.edit' || Route::currentRouteName() == 'admin.branch.userview' || Route::currentRouteName() == 'admin.branch.clientview') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.branch.index')}}">Offices</a></li>
			<?php } ?>
			<?php
			if(array_key_exists('4',  $module_access)) {
			?>
			<li class="{{(Route::currentRouteName() == 'admin.users.active' || Route::currentRouteName() == 'admin.users.inactive' || Route::currentRouteName() == 'admin.users.invited') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.users.active')}}">Users</a></li>
			<li class="{{(Route::currentRouteName() == 'admin.teams.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.teams.index')}}">Teams</a></li>
			<?php } ?>
			<?php
			if(array_key_exists('6',  $module_access)) {
			?>
			<li class="{{(Route::currentRouteName() == 'admin.userrole.index' || Route::currentRouteName() == 'admin.userrole.create' || Route::currentRouteName() == 'admin.userrole.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.userrole.index')}}">Roles</a></li>
			<?php } ?>
			<li class="{{(Route::currentRouteName() == 'admin.gensettings.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.gensettings.index')}}">Gen Settings</a></li>
			

            <li class="{{(Route::currentRouteName() == 'admin.feature.appointmentdisabledate.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.feature.appointmentdisabledate.index')}}">Appointment Dates Not Available</a></li>
            <li class="{{(Route::currentRouteName() == 'admin.feature.promocode.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.feature.promocode.index')}}">Promo Code</a></li>

			<li class="{{(Route::currentRouteName() == 'admin.feature.personaldocumenttype.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.feature.personaldocumenttype.index')}}">Personal Document Category</a></li>

            <li class="{{(Route::currentRouteName() == 'admin.feature.visadocumenttype.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.feature.visadocumenttype.index')}}">Visa Document Category</a></li>

			<li class="{{(Route::currentRouteName() == 'admin.feature.documentchecklist.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.feature.documentchecklist.index')}}">Document Checklist</a></li>

			<li class="{{(Route::currentRouteName() == 'admin.anzsco.index' || Route::currentRouteName() == 'admin.anzsco.create' || Route::currentRouteName() == 'admin.anzsco.edit' || Route::currentRouteName() == 'admin.anzsco.import') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.anzsco.index')}}">ANZSCO Database</a></li>

			
			<li class="{{(Route::currentRouteName() == 'admin.feature.matter.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.feature.matter.index')}}">Matter List</a></li>
			
		</ul>
</div>

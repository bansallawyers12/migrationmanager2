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
		<li class="{{(Route::currentRouteName() == 'adminconsole.features.tags.index' || Route::currentRouteName() == 'adminconsole.features.tags.create' || Route::currentRouteName() == 'adminconsole.features.tags.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.tags.index')}}">Tags</a></li>
		{{-- Commented out routes that don't exist yet
		<li class="{{(Route::currentRouteName() == 'admin.checklist.index' || Route::currentRouteName() == 'admin.checklist.create' || Route::currentRouteName() == 'admin.checklist.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.checklist.index')}}">Checklist</a></li>
		<li class="{{(Route::currentRouteName() == 'admin.enquirysource.index' || Route::currentRouteName() == 'admin.enquirysource.create' || Route::currentRouteName() == 'admin.enquirysource.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.enquirysource.index')}}">Enquiry Source</a></li>
		--}}

        <li class="{{(Route::currentRouteName() == 'adminconsole.features.workflow.index' || Route::currentRouteName() == 'adminconsole.features.workflow.create' || Route::currentRouteName() == 'adminconsole.features.workflow.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.workflow.index')}}">Workflow</a></li>

        <li class="{{(Route::currentRouteName() == 'adminconsole.features.emails.index' || Route::currentRouteName() == 'adminconsole.features.emails.create' || Route::currentRouteName() == 'adminconsole.features.emails.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.emails.index')}}">Email</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.features.crmemailtemplate.index' || Route::currentRouteName() == 'adminconsole.features.crmemailtemplate.create' || Route::currentRouteName() == 'adminconsole.features.crmemailtemplate.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.crmemailtemplate.index')}}">Crm Email Template</a></li>

		<?php
			if(Route::currentRouteName() == 'adminconsole.system.offices.index' || Route::currentRouteName() == 'adminconsole.system.offices.create' || Route::currentRouteName() == 'adminconsole.system.offices.edit' || Route::currentRouteName() == 'adminconsole.system.offices.view' || Route::currentRouteName() == 'adminconsole.system.offices.viewclient' || Route::currentRouteName() == 'adminconsole.system.users.active' || Route::currentRouteName() == 'adminconsole.system.users.inactive' || Route::currentRouteName() == 'adminconsole.system.users.invited' || Route::currentRouteName() == 'adminconsole.system.roles.index' || Route::currentRouteName() == 'adminconsole.system.roles.create' || Route::currentRouteName() == 'adminconsole.system.roles.edit'){
				$teamclasstype = 'active';
			}
		?>
			<?php
			if(array_key_exists('1',  $module_access)) {
			?>
			<li class="{{(Route::currentRouteName() == 'adminconsole.system.offices.index' || Route::currentRouteName() == 'adminconsole.system.offices.create' || Route::currentRouteName() == 'adminconsole.system.offices.edit' || Route::currentRouteName() == 'adminconsole.system.offices.view' || Route::currentRouteName() == 'adminconsole.system.offices.viewclient') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.system.offices.index')}}">Offices</a></li>
			<?php } ?>
			<?php
			if(array_key_exists('4',  $module_access)) {
			?>
			<li class="{{(Route::currentRouteName() == 'adminconsole.system.users.active' || Route::currentRouteName() == 'adminconsole.system.users.inactive' || Route::currentRouteName() == 'adminconsole.system.users.invited') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.system.users.active')}}">Users</a></li>
			<li class="{{(Route::currentRouteName() == 'adminconsole.system.teams.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.system.teams.index')}}">Teams</a></li>
			<?php } ?>
			<?php
			if(array_key_exists('6',  $module_access)) {
			?>
			<li class="{{(Route::currentRouteName() == 'adminconsole.system.roles.index' || Route::currentRouteName() == 'adminconsole.system.roles.create' || Route::currentRouteName() == 'adminconsole.system.roles.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.system.roles.index')}}">Roles</a></li>
			<?php } ?>
			<li class="{{(Route::currentRouteName() == 'adminconsole.system.settings.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.system.settings.index')}}">Gen Settings</a></li>
			

            <li class="{{(Route::currentRouteName() == 'adminconsole.features.appointmentdisabledate.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.appointmentdisabledate.index')}}">Appointment Dates Not Available</a></li>
            <li class="{{(Route::currentRouteName() == 'adminconsole.features.promocode.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.promocode.index')}}">Promo Code</a></li>

			<li class="{{(Route::currentRouteName() == 'adminconsole.features.personaldocumenttype.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.personaldocumenttype.index')}}">Personal Document Category</a></li>

            <li class="{{(Route::currentRouteName() == 'adminconsole.features.visadocumenttype.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.visadocumenttype.index')}}">Visa Document Category</a></li>

			<li class="{{(Route::currentRouteName() == 'adminconsole.features.documentchecklist.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.documentchecklist.index')}}">Document Checklist</a></li>

			<li class="{{(Route::currentRouteName() == 'adminconsole.database.anzsco.index' || Route::currentRouteName() == 'adminconsole.database.anzsco.create' || Route::currentRouteName() == 'adminconsole.database.anzsco.edit' || Route::currentRouteName() == 'adminconsole.database.anzsco.import') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.database.anzsco.index')}}">ANZSCO Database</a></li>

			
			<li class="{{(Route::currentRouteName() == 'adminconsole.features.matter.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.matter.index')}}">Matter List</a></li>
			
		</ul>
</div>

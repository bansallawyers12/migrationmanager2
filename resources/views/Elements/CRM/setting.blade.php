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
			if(Route::currentRouteName() == 'adminconsole.features.producttype.index' || Route::currentRouteName() == 'adminconsole.features.producttype.create' || Route::currentRouteName() == 'adminconsole.features.producttype.edit' || Route::currentRouteName() == 'adminconsole.features.visatype.index' || Route::currentRouteName() == 'adminconsole.features.visatype.create' || Route::currentRouteName() == 'adminconsole.features.visatype.edit' || Route::currentRouteName() == 'adminconsole.features.source.index' || Route::currentRouteName() == 'adminconsole.features.source.create' || Route::currentRouteName() == 'adminconsole.features.source.edit' || Route::currentRouteName() == 'adminconsole.features.tags.index' || Route::currentRouteName() == 'adminconsole.features.tags.create' || Route::currentRouteName() == 'adminconsole.features.tags.edit' || Route::currentRouteName() == 'adminconsole.features.emaillabels.index' || Route::currentRouteName() == 'adminconsole.features.emaillabels.create' || Route::currentRouteName() == 'adminconsole.features.emaillabels.edit' || Route::currentRouteName() == 'adminconsole.features.workflow.index' || Route::currentRouteName() == 'adminconsole.features.workflow.create' || Route::currentRouteName() == 'adminconsole.features.workflow.edit' || str_starts_with(Route::currentRouteName() ?? '', 'adminconsole.features.workflow.')){
				$addfeatureclasstype = 'active';
		}
		?>
		{{--
		<li class="{{(Route::currentRouteName() == 'crm.feature.producttype.index' || Route::currentRouteName() == 'crm.feature.producttype.create' || Route::currentRouteName() == 'crm.feature.producttype.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('crm.feature.producttype.index')}}">Product Type</a></li>
		<li class="{{(Route::currentRouteName() == 'crm.feature.mastercategory.index' || Route::currentRouteName() == 'crm.feature.mastercategory.create' || Route::currentRouteName() == 'crm.feature.mastercategory.edit' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('crm.feature.mastercategory.index')}}">Master Category</a></li>
    	<li class="{{(Route::currentRouteName() == 'crm.feature.visatype.index' || Route::currentRouteName() == 'crm.feature.visatype.create' || Route::currentRouteName() == 'crm.feature.visatype.edit' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('crm.feature.visatype.index')}}">Visa Type</a></li>
		<li class="{{(Route::currentRouteName() == 'crm.feature.source.index' || Route::currentRouteName() == 'crm.feature.source.create' || Route::currentRouteName() == 'crm.feature.source.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('crm.feature.source.index')}}">Source</a></li>
		--}}
		<li class="{{(Route::currentRouteName() == 'adminconsole.features.tags.index' || Route::currentRouteName() == 'adminconsole.features.tags.create' || Route::currentRouteName() == 'adminconsole.features.tags.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.tags.index')}}">Tags</a></li>
		
		<li class="{{(Route::currentRouteName() == 'adminconsole.features.emaillabels.index' || Route::currentRouteName() == 'adminconsole.features.emaillabels.create' || Route::currentRouteName() == 'adminconsole.features.emaillabels.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.emaillabels.index')}}">Email Labels</a></li>
		
		{{-- Old admin routes replaced with AdminConsole routes (see line 60 for Document Checklist)
		<li class="{{(Route::currentRouteName() == 'adminconsole.features.documentchecklist.index' || Route::currentRouteName() == 'adminconsole.features.documentchecklist.create' || Route::currentRouteName() == 'adminconsole.features.documentchecklist.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.documentchecklist.index')}}">Document Checklist</a></li>
		<li class="{{(Route::currentRouteName() == 'crm.enquirysource.index' || Route::currentRouteName() == 'crm.enquirysource.create' || Route::currentRouteName() == 'crm.enquirysource.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('crm.enquirysource.index')}}">Enquiry Source</a></li>
		--}}

        <li class="{{(str_starts_with(Route::currentRouteName() ?? '', 'adminconsole.features.workflow.')) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.workflow.index')}}">Workflows</a></li>

        <li class="{{(Route::currentRouteName() == 'adminconsole.features.emails.index' || Route::currentRouteName() == 'adminconsole.features.emails.create' || Route::currentRouteName() == 'adminconsole.features.emails.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.emails.index')}}">Email</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.features.crmemailtemplate.index' || Route::currentRouteName() == 'adminconsole.features.crmemailtemplate.create' || Route::currentRouteName() == 'adminconsole.features.crmemailtemplate.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.crmemailtemplate.index')}}">Crm Email Template</a></li>

		<?php
			if(Route::currentRouteName() == 'adminconsole.system.offices.index' || Route::currentRouteName() == 'adminconsole.system.offices.create' || Route::currentRouteName() == 'adminconsole.system.offices.edit' || Route::currentRouteName() == 'adminconsole.system.offices.view' || Route::currentRouteName() == 'adminconsole.system.offices.viewclient' || Route::currentRouteName() == 'adminconsole.staff.active' || Route::currentRouteName() == 'adminconsole.staff.inactive' || Route::currentRouteName() == 'adminconsole.staff.invited' || Route::currentRouteName() == 'adminconsole.staff.create' || Route::currentRouteName() == 'adminconsole.staff.edit' || Route::currentRouteName() == 'adminconsole.staff.view' || Route::currentRouteName() == 'adminconsole.system.clients.clientlist' || Route::currentRouteName() == 'adminconsole.system.clients.createclient' || Route::currentRouteName() == 'adminconsole.system.clients.editclient' || Route::currentRouteName() == 'adminconsole.system.roles.index' || Route::currentRouteName() == 'adminconsole.system.roles.create' || Route::currentRouteName() == 'adminconsole.system.roles.edit'){
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
			<li class="{{(Route::currentRouteName() == 'adminconsole.staff.active' || Route::currentRouteName() == 'adminconsole.staff.inactive' || Route::currentRouteName() == 'adminconsole.staff.invited' || Route::currentRouteName() == 'adminconsole.staff.create' || Route::currentRouteName() == 'adminconsole.staff.edit' || Route::currentRouteName() == 'adminconsole.staff.view') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.staff.active')}}">Staff</a></li>
			<li class="{{(Route::currentRouteName() == 'adminconsole.system.clients.clientlist' || Route::currentRouteName() == 'adminconsole.system.clients.createclient' || Route::currentRouteName() == 'adminconsole.system.clients.editclient') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.system.clients.clientlist')}}">Clients</a></li>
			<li class="{{(Route::currentRouteName() == 'adminconsole.system.teams.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.system.teams.index')}}">Teams</a></li>
			<?php } ?>
			<?php
			if(array_key_exists('6',  $module_access)) {
			?>
			<li class="{{(Route::currentRouteName() == 'adminconsole.system.roles.index' || Route::currentRouteName() == 'adminconsole.system.roles.create' || Route::currentRouteName() == 'adminconsole.system.roles.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.system.roles.index')}}">Roles</a></li>
			<?php } ?>
			
			<li class="{{(Route::currentRouteName() == 'adminconsole.features.personaldocumenttype.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.personaldocumenttype.index')}}">Personal Document Category</a></li>

            <li class="{{(Route::currentRouteName() == 'adminconsole.features.visadocumenttype.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.visadocumenttype.index')}}">Visa Document Category</a></li>

			<li class="{{(Route::currentRouteName() == 'adminconsole.features.documentchecklist.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.documentchecklist.index')}}">Document Checklist</a></li>

			<li class="{{(Route::currentRouteName() == 'adminconsole.database.anzsco.index' || Route::currentRouteName() == 'adminconsole.database.anzsco.create' || Route::currentRouteName() == 'adminconsole.database.anzsco.edit' || Route::currentRouteName() == 'adminconsole.database.anzsco.import') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.database.anzsco.index')}}">ANZSCO Database</a></li>

			
			<li class="{{(Route::currentRouteName() == 'adminconsole.features.matter.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.features.matter.index')}}">Matter List</a></li>

			<?php
			// SMS Management menu - Only for super admin
			if(Auth::user()->role == 1) {
				$smsclasstype = '';
				if(str_starts_with(Route::currentRouteName() ?? '', 'adminconsole.features.sms.')){
					$smsclasstype = 'active';
				}
			?>
			<li class="{{$smsclasstype}}"><a class="nav-link" href="{{route('adminconsole.features.sms.dashboard')}}">SMS Management</a></li>
			<?php
			}
			?>

			<?php
			// E-Signature Management menu - Available for all admin users
			$esignatureclasstype = '';
			if(str_starts_with(Route::currentRouteName() ?? '', 'adminconsole.features.esignature.')){
				$esignatureclasstype = 'active';
			}
			?>
			<li class="{{$esignatureclasstype}}"><a class="nav-link" href="{{route('adminconsole.features.esignature.index')}}">E-Signature</a></li>
			
			<?php
			// Activity Search menu - Only for super admin (role = 1)
			if(Auth::user()->role == 1) {
				$activitySearchclasstype = '';
				if(str_starts_with(Route::currentRouteName() ?? '', 'adminconsole.system.activity-search.')){
					$activitySearchclasstype = 'active';
				}
			?>
			<li class="{{$activitySearchclasstype}}"><a class="nav-link" href="{{route('adminconsole.system.activity-search.index')}}">Activity Search</a></li>
			<?php
			}
			?>
			
		</ul>
</div>

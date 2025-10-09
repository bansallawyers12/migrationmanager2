# Simple route fix script
$file = "routes\web.php"
$content = Get-Content $file -Raw

# Replace each controller one by one
$content = $content.Replace("'Admin\UserController", "'AdminConsole\UserController")
$content = $content.Replace("'Admin\UserroleController", "'AdminConsole\UserroleController")
$content = $content.Replace("'Admin\TeamController", "'AdminConsole\TeamController")
$content = $content.Replace("'Admin\BranchesController", "'AdminConsole\BranchesController")
$content = $content.Replace("'Admin\MatterController", "'AdminConsole\MatterController")
$content = $content.Replace("'Admin\TagController", "'AdminConsole\TagController")
$content = $content.Replace("'Admin\WorkflowController", "'AdminConsole\WorkflowController")
$content = $content.Replace("'Admin\EmailController", "'AdminConsole\EmailController")
$content = $content.Replace("'Admin\CrmEmailTemplateController", "'AdminConsole\CrmEmailTemplateController")
$content = $content.Replace("'Admin\MatterEmailTemplateController", "'AdminConsole\MatterEmailTemplateController")
$content = $content.Replace("'Admin\MatterOtherEmailTemplateController", "'AdminConsole\MatterOtherEmailTemplateController")
$content = $content.Replace("'Admin\PersonalDocumentTypeController", "'AdminConsole\PersonalDocumentTypeController")
$content = $content.Replace("'Admin\VisaDocumentTypeController", "'AdminConsole\VisaDocumentTypeController")
$content = $content.Replace("'Admin\DocumentChecklistController", "'AdminConsole\DocumentChecklistController")
$content = $content.Replace("'Admin\AppointmentDisableDateController", "'AdminConsole\AppointmentDisableDateController")
$content = $content.Replace("'Admin\PromoCodeController", "'AdminConsole\PromoCodeController")
$content = $content.Replace("'Admin\ProfileController", "'AdminConsole\ProfileController")
$content = $content.Replace("'Admin\AnzscoOccupationController", "'AdminConsole\AnzscoOccupationController")

Set-Content $file $content -NoNewline

Write-Host "Routes updated successfully!" -ForegroundColor Green

# Test
php artisan route:clear
php artisan route:list --name=adminconsole

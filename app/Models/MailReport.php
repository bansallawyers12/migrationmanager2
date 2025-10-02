<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class MailReport extends Authenticatable
{
    use Notifiable;
	use Sortable;

	protected $fillable = ['id','user_id','from_mail','to_mail','cc','template_id','subject','message','type','reciept_id','attachments','mail_type','client_id','client_matter_id','conversion_type','mail_body_type','fetch_mail_sent_time','uploaded_doc_id','created_at', 'updated_at'];

	public $sortable = ['id', 'created_at', 'updated_at'];

}

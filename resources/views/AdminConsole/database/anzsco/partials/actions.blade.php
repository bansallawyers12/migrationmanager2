<div class="btn-group btn-group-sm">
    <a href="{{ route('adminconsole.database.anzsco.edit', $occupation->id) }}" 
       class="btn btn-info" title="Edit" aria-label="Edit">
        @icon('fa-edit')
    </a>
    <button type="button" class="btn btn-danger delete-occupation" 
            data-id="{{ $occupation->id }}" 
            data-title="{{ $occupation->occupation_title }}"
            title="Delete" aria-label="Delete">
        @icon('fa-trash')
    </button>
</div>


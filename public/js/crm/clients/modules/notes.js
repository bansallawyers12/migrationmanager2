/**
 * Notes module - Create, edit, view notes; getallnotes; Select2 format helpers
 * Extracted from detail-main.js - Phase 3b refactoring.
 * Requires: jQuery, ClientDetailConfig, clearEditor, setEditorContent, adjustActivityFeedHeight
 */
(function($) {
    'use strict';
    if (!$) return;

    var baseUrl = (typeof site_url !== 'undefined' ? site_url : (window.ClientDetailConfig && window.ClientDetailConfig.urls && window.ClientDetailConfig.urls.base ? window.ClientDetailConfig.urls.base : ''));

    function formatRepo(repo) {
        if (repo.loading) {
            return repo.text;
        }
        var $container = $(
            "<div class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +
            "<div class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
            "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small></div></div></div>" +
            "<div class='ag-flex ag-flex-column ag-align-end'><span class='ui label yellow select2-result-repository__statistics'></span></div></div>"
        );
        $container.find(".select2-result-repository__title").text(repo.name);
        $container.find(".select2-result-repository__description").text(repo.email);
        $container.find(".select2-result-repository__statistics").append(repo.status);
        return $container;
    }

    function formatRepoSelection(repo) {
        return repo.name || repo.text;
    }

    window.formatRepo = formatRepo;
    window.formatRepoSelection = formatRepoSelection;

    function getallnotes() {
        var notesUrl = (window.ClientDetailConfig && window.ClientDetailConfig.urls && window.ClientDetailConfig.urls.getNotes) ? window.ClientDetailConfig.urls.getNotes : baseUrl + '/get-notes';
        $.ajax({
            url: notesUrl,
            type: 'GET',
            data: { clientid: window.ClientDetailConfig.clientId, type: 'client' },
            success: function(responses) {
                $('.popuploader').hide();
                $('.note_term_list').html(responses);
                var selectedMatter = $('.general_matter_checkbox_client_detail').is(':checked') ? $('.general_matter_checkbox_client_detail').val() : $('#sel_matter_id_client_detail').val();
                var activeTaskGroup = $('.subtab8-button.active').data('subtab8') || 'All';
                if (!$('.subtab8-button.active').length) {
                    $('.subtab8-button.pill-tab[data-subtab8="All"]').addClass('active');
                    $('#noteterm-tab').find('.note-card-redesign').show();
                } else {
                    $('#noteterm-tab').find('.note-card-redesign').each(function() {
                        var noteMatterId = $(this).data('matterid');
                        var noteType = $(this).data('type');
                        var showNote = false;
                        if (selectedMatter !== "") {
                            showNote = (noteMatterId == selectedMatter || noteMatterId == '' || noteMatterId == null);
                        } else {
                            showNote = true;
                        }
                        if (showNote && activeTaskGroup !== 'All') {
                            showNote = (noteType === activeTaskGroup);
                        }
                        if (showNote) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
                if (typeof adjustActivityFeedHeight === 'function') {
                    adjustActivityFeedHeight();
                }
            }
        });
    }

    window.getallnotes = getallnotes;

    $(document).ready(function() {
        $(document).delegate('.create_note_d', 'click', function() {
            $('#create_note_d').modal('show');
            $('#create_note_d input[name="mailid"]').val(0);
            $('#create_note_d input[name="title"]').val("Matter Discussion");
            $('#create_note_d #appliationModalLabel').html('Create Note');
            if ($(this).attr('datatype') == 'note') {
                $('.is_not_note').hide();
            } else {
                var datasubject = $(this).attr('datasubject');
                var datamailid = $(this).attr('datamailid');
                $('#create_note_d input[name="title"]').val(datasubject);
                $('#create_note_d input[name="mailid"]').val(datamailid);
                $('.is_not_note').show();
            }
        });

        $(document).delegate('.create_note', 'click', function() {
            $('#create_note').modal('show');
            $('#create_note input[name="mailid"]').val(0);
            $('#create_note input[name="title"]').val('');
            $('#create_note #appliationModalLabel').html('Create Note');
            $('#create_note input[name="noteid"]').val('');
            if (typeof clearEditor === 'function') {
                clearEditor("#create_note .summernote-simple");
            }
            $("#create_note .summernote-simple").val('');
            if ($(this).attr('datatype') == 'note') {
                $('.is_not_note').hide();
            } else {
                var datasubject = $(this).attr('datasubject');
                var datamailid = $(this).attr('datamailid');
                $('#create_note input[name="title"]').val(datasubject);
                $('#create_note input[name="mailid"]').val(datamailid);
                $('.is_not_note').show();
            }
        });

        if ($('#create_note').length && $('.js-data-example-ajaxcc').length) {
            $('.js-data-example-ajaxcc').select2({
                multiple: true,
                closeOnSelect: false,
                dropdownParent: $('#create_note'),
                ajax: {
                    url: window.ClientDetailConfig.urls.getRecipients,
                    dataType: 'json',
                    processResults: function(data) {
                        return { results: data.items };
                    },
                    cache: true
                },
                templateResult: formatRepo,
                templateSelection: formatRepoSelection
            });
        }

        $(document).on('click', '.opennoteform', function(e) {
            e.preventDefault();
            if ($('#create_note').length === 0) {
                console.error('Modal #create_note not found!');
                return;
            }
            $('#create_note').modal('show');
            $('#create_note #appliationModalLabel').html('Edit Note');
            var v = $(this).attr('data-id');
            $('#create_note input[name="noteid"]').val(v);
            $('.popuploader').show();
            $.ajax({
                url: window.ClientDetailConfig.urls.getNoteDetail,
                type: 'GET',
                datatype: 'json',
                data: { note_id: v },
                success: function(response) {
                    $('.popuploader').hide();
                    var res = JSON.parse(response);
                    if (res.status) {
                        $('#create_note select[name="task_group"]').val(res.data.task_group);
                        $("#create_note .summernote-simple").val(res.data.description);
                        if (typeof setEditorContent === 'function') {
                            setEditorContent("#create_note .summernote-simple", res.data.description);
                        }
                    } else {
                        console.error('Note details not found or error in response');
                    }
                },
                error: function(xhr, status, error) {
                    $('.popuploader').hide();
                    console.error('Error fetching note details:', error);
                }
            });
        });

        $(document).delegate('.viewnote', 'click', function() {
            $('#view_note').modal('show');
            var v = $(this).attr('data-id');
            $('#view_note input[name="noteid"]').val(v);
            $('.popuploader').show();
            $.ajax({
                url: window.ClientDetailConfig.urls.viewNoteDetail,
                type: 'GET',
                datatype: 'json',
                data: { note_id: v },
                success: function(response) {
                    $('.popuploader').hide();
                    var res = JSON.parse(response);
                    if (res.status) {
                        $('#view_note .modal-body .note_content h5').html(res.data.title);
                        $("#view_note .modal-body .note_content p").html(res.data.description);
                    }
                }
            });
        });

        $(document).delegate('.viewapplicationnote', 'click', function() {
            $('#view_application_note').modal('show');
            var v = $(this).attr('data-id');
            $('#view_application_note input[name="noteid"]').val(v);
            $('.popuploader').show();
            $.ajax({
                url: window.ClientDetailConfig.urls.viewApplicationNote,
                type: 'GET',
                datatype: 'json',
                data: { note_id: v },
                success: function(response) {
                    $('.popuploader').hide();
                    var res = JSON.parse(response);
                    if (res.status) {
                        $('#view_application_note .modal-body .note_content h5').html(res.data.title);
                        $("#view_application_note .modal-body .note_content p").html(res.data.description);
                    }
                }
            });
        });
    });

})(typeof jQuery !== 'undefined' ? jQuery : null);

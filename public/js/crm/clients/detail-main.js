    // Global flag to prevent redirects during page initialization

    var isInitializing = true;



    // Global function for adjusting activity feed height

    function adjustActivityFeedHeight() {

        // Safety check - ensure required DOM elements exist

        if (!$('.activity-feed').length || !$('.main-content').length || !$('.crm-container').length) {

            return; // Exit silently if elements don't exist (DOM not ready)

        }

        

        // Calculate maximum available height (viewport minus header/navigation)

        let windowHeight = $(window).height();

        let maxAvailableHeight = windowHeight - 120; // Account for header and navigation

        

        // Force container to recalculate layout

        $('.crm-container').css('align-items', 'flex-start');

        

        // Reset main-content to allow natural height

        $('.main-content').css('max-height', 'none');

        $('.main-content').css('overflow-y', 'visible');

        $('.main-content').css('height', 'auto');

        

        // Get the actual main-content height after reset

        let mainContentHeight = $('.main-content').outerHeight(); console.log(mainContentHeight);

        

        // Get Activity Feed content height to determine if it has data
        let activityFeedContentHeight = $('.activity-feed').prop('scrollHeight');
        let activityFeedVisibleHeight = $('.activity-feed').outerHeight();
        
        // Check if Activity Feed has substantial content (more than just empty space)
        let hasSubstantialContent = activityFeedContentHeight > 100; // Adjust threshold as needed
        
        console.log('Activity Feed content height:', activityFeedContentHeight);
        console.log('Activity Feed visible height:', activityFeedVisibleHeight);
        console.log('Has substantial content:', hasSubstantialContent);

        // Set Activity Feed height based on content availability
        let targetHeight;
        if (hasSubstantialContent) {
            // When Activity Feed has large content, use Math.max to ensure it can expand
            targetHeight = Math.max(mainContentHeight, maxAvailableHeight);
        } else {
            // When Activity Feed has no data or less data, use Math.min to prevent large vacant space
            targetHeight = Math.min(mainContentHeight, maxAvailableHeight);
        }
        console.log('Target height:', targetHeight);
        

        // Set Activity Feed height dynamically

        $('.activity-feed').css('max-height', targetHeight + 'px');

        $('.activity-feed').css('height', targetHeight + 'px');

        

        // Ensure proper overflow handling

        $('.activity-feed').css('overflow-y', 'auto');

    }



    $(document).ready(function() {

        

        // Flag to prevent redirects during page initialization (now global)



        // Run on load

        adjustActivityFeedHeight();



        // Run on resize (for responsiveness)

        $(window).on('resize', function () {

            adjustActivityFeedHeight();

        });



        //Update Personal Doucment Category

        $('.update-personal-cat-title').on('click', function() {

            var id = $(this).data('id');

            var newTitle = prompt('Enter new title for the category:');

            if (newTitle) {

                $.ajax({

                    url: '/documents/update-personal-category',

                    method: 'POST',

                    data: {

                        _token: $('meta[name="csrf-token"]').attr('content'),

                        id: id,

                        title: newTitle

                    },

                    success: function(response) {

                        if (response.status) {

                            alert(response.message);

                            location.reload();

                        } else {

                            //alert('Failed to update title.');

                            alert(response.message);

                        }

                    }

                });

            }

        });



        /*$('.delete-personal-cat-title').on('click', function() {

            var id = $(this).data('id');

            if (confirm('Are you sure you want to delete this category?')) {

                $.ajax({

                    url: '/delete-personal-doc-category',

                    method: 'POST',

                    data: {

                        _token: $('meta[name="csrf-token"]').attr('content'),

                        id: id

                    },

                    success: function(response) {

                        if (response.success) {

                            location.reload();

                        } else {

                            alert('Failed to delete category.');

                        }

                    }

                });

            }

        });*/



        //Update Visa Doucment Category

        $('.update-visa-cat-title').on('click', function() {

            var id = $(this).data('id');

            var newTitle = prompt('Enter new title for the category:');

            if (newTitle) {

                $.ajax({

                    url: '/documents/update-visa-category',

                    method: 'POST',

                    data: {

                        _token: $('meta[name="csrf-token"]').attr('content'),

                        id: id,

                        title: newTitle

                    },

                    success: function(response) {

                        if (response.status) {

                            alert(response.message);

                            location.reload();

                        } else {

                            //alert('Failed to update title.');

                            alert(response.message);

                        }

                    }

                });

            }

        });



        //Save reference click

        $(document).delegate('.saveReferenceValue', 'click', function() {

            let department_reference = $('#department_reference').val();

            let other_reference = $('#other_reference').val();

            let client_id = window.ClientDetailConfig.clientId;

            let selectedMatter = $('#sel_matter_id_client_detail').val();

            if(department_reference == '' || other_reference == ''){

                alert('Please enter department reference, other refence value');

            } else {

                $.ajax({

                    url: window.ClientDetailConfig.urls.referencesStore,

                    type: 'POST',

                    data: {

                        department_reference: department_reference,

                        other_reference: other_reference,

                        client_id: client_id,

                        client_matter_id:selectedMatter,

                        _token: window.ClientDetailConfig.csrfToken

                    },

                    success: function (response) {

                        alert('Saved successfully');

                        location.reload(); //Page reload after success

                    },

                    error: function (xhr) {

                        alert('Error saving data');

                        console.error(xhr.responseText);

                    }

                });

            }

        });



        function load_visa_expiry_messages(client_id,view = '') {

            var playing = false;

            $.ajax({

                url: window.ClientDetailConfig.urls.fetchVisaExpiryMessages,

                method:"GET",

                data: { client_id:client_id},

                success:function(data) {

                    if(data != 0){

                        iziToast.show({

                            backgroundColor: 'rgba(0,0,255,0.3)',

                            messageColor: 'rgba(255,255,255)',

                            title: '',

                            message: data,

                            position: 'bottomRight'

                        });

                        $(this).toggleClass("down");



                        if (playing == false) {

                            document.getElementById('player').play();

                            playing = true;

                            $(this).text("stop sound");



                        } else {

                            document.getElementById('player').pause();

                            playing = false;

                            $(this).text("restart sound");

                        }

                    }

                }

            });

        }



        setInterval(function(){

            var client_id = window.ClientDetailConfig.clientId;

            load_visa_expiry_messages(client_id);

        },900000 ); //15 min interval





       // On page load, check if the URL contains a matter ID and set the dropdown/checkbox state

        var currentUrl = window.location.href;

        var urlSegments = currentUrl.split('/');

        var matterIdInUrl = urlSegments.length > 7 ? urlSegments[urlSegments.length - 1] : null;



        if (matterIdInUrl === null) {

            // Case 1: No matter ID in URL - Don't auto-select any matter

            selectedMatter = '';

            //console.log('No matter ID in URL, no matter selected - showing all notes');

        }

        else {

            // Case 2: Matter ID exists in URL

            let matchFound = false;



            // a) First check the dropdown for a matching option

            $('#sel_matter_id_client_detail option').each(function() {

                var uniqueMatterNo = $(this).data('clientuniquematterno');

                if (uniqueMatterNo === matterIdInUrl) {

                    $('#sel_matter_id_client_detail').val($(this).val()).trigger('change');

                    selectedMatter = $(this).val();

                    matchFound = true;

                    //console.log('Matter ID found in URL, selected matching dropdown option:', selectedMatter);

                }

            });



            // If no matching option in dropdown, proceed with further checks

            if (!matchFound) {

                // b) Check for a matching checkbox

                let checkboxMatchFound = false;

                $('.general_matter_checkbox_client_detail').each(function() {

                    var uniqueMatterNo = $(this).data('clientuniquematterno');

                    if (uniqueMatterNo === matterIdInUrl) {

                        $(this).prop('checked', true).trigger('change');

                        selectedMatter = $(this).val();

                        checkboxMatchFound = true;

                        //console.log('Matter ID in URL, checked matching checkbox:', selectedMatter);

                        return false; // Exit the loop once a match is found

                    }

                });



                // If no matching checkbox, check the first non-empty dropdown option

                if (!checkboxMatchFound) {

                    let firstNonEmptyOption = $('#sel_matter_id_client_detail option').filter(function() {

                        return $(this).val() !== '';

                    }).first();



                    if (firstNonEmptyOption.length) {

                        // If a non-empty option exists in the dropdown, select it

                        $('#sel_matter_id_client_detail').val(firstNonEmptyOption.val()).trigger('change');

                        selectedMatter = firstNonEmptyOption.val();

                        //console.log('Matter ID in URL, no checkbox match, selected first non-empty dropdown option:', selectedMatter);

                    } else {

                        // If no non-empty dropdown options, check the first checkbox

                        let firstCheckbox = $('.general_matter_checkbox_client_detail').first();

                        if (firstCheckbox.length) {

                            firstCheckbox.prop('checked', true).trigger('change');

                            selectedMatter = firstCheckbox.val();

                            //console.log('Matter ID in URL, no dropdown match, checked first checkbox:', selectedMatter);

                        } else {

                            selectedMatter = '';

                            //console.log('Matter ID in URL, no matches in dropdown or checkboxes');

                        }

                    }

                }

            }

        }

        

        // Set flag to false after initialization is complete

        setTimeout(function() {

            isInitializing = false;

        }, 100);



        // When Matter AI tab is clicked









        // Activity Feed Width Toggle

        $('#increase-activity-feed-width').on('change', function() {

            if ($(this).is(':checked')) {

                $('.activity-feed').addClass('wide-mode');

                $('.main-content').addClass('compact-mode');

            } else {

                $('.activity-feed').removeClass('wide-mode');

                $('.main-content').removeClass('compact-mode');

            }

            

            // Adjust Activity Feed height after layout change

            adjustActivityFeedHeight();

            

            // Force a small delay to ensure CSS transitions complete

            setTimeout(function() {

                adjustActivityFeedHeight();

            }, 150);

        });



    });



    // REMOVED: Duplicate tab switching code - now handled by sidebar-tabs.js



    //For download document - jQuery version for better compatibility

    $(document).ready(function() {

        // COMMENTED OUT - Using the direct event handler below instead

        

        $(document).on('click', '.download-file', function(e) {

            e.preventDefault();

            e.stopPropagation();



            const $this = $(this);

            const filelink = $this.data('filelink');

            const filename = $this.data('filename');



          



            if (!filelink || !filename) {

                console.error('Missing file info - filelink:', filelink, 'filename:', filename);

                alert('Missing file info. Please try again.');

                return false;

            }



            // Show loading indicator

            $this.html('<i class="fas fa-spinner fa-spin"></i> Downloading...');

            $this.prop('disabled', true);



            // Create and submit a hidden form

            const form = $('<form>', {

                method: 'POST',

                action: window.ClientDetailConfig.urls.downloadDocument,

                target: '_blank',

                style: 'display: none;'

            });



            // CSRF token

            const token = $('meta[name="csrf-token"]').attr('content');

            if (!token) {

                console.error('CSRF token not found');

                alert('Security token not found. Please refresh the page and try again.');

                $this.html('Download').prop('disabled', false);

                return false;

            }



            // Add form fields

            form.append($('<input>', {

                type: 'hidden',

                name: '_token',

                value: token

            }));



            form.append($('<input>', {

                type: 'hidden',

                name: 'filelink',

                value: filelink

            }));



            form.append($('<input>', {

                type: 'hidden',

                name: 'filename',

                value: filename

            }));



         



            // Append form to body and submit

            $('body').append(form);

            

            try {

                form[0].submit();

                

                // Reset button after a short delay

                setTimeout(function() {

                    $this.html('Download').prop('disabled', false);

                }, 2000);

                

            } catch (error) {

                console.error('Error submitting form:', error);

                alert('Error initiating download. Please try again.');

                $this.html('Download').prop('disabled', false);

            }



            // Remove form after submission

            setTimeout(function() {

                form.remove();

            }, 1000);



            return false;

        });

        

    });



    // Alternative vanilla JavaScript version as backup

    document.addEventListener('DOMContentLoaded', function () {

        document.addEventListener('click', function (e) {

            const target = e.target.closest('a.download-file');

            if (!target) return;



            e.preventDefault();

            e.stopPropagation();



    



            const filelink = target.dataset.filelink;

            const filename = target.dataset.filename;



           



            if (!filelink || !filename) {

                console.error('Missing file info - filelink:', filelink, 'filename:', filename);

                alert('Missing file info. Please try again.');

                return;

            }



            // Show loading state

            const originalText = target.innerHTML;

            target.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Downloading...';

            target.style.pointerEvents = 'none';



            // Create and submit a hidden form

            const form = document.createElement('form');

            form.method = 'POST';

            form.action = window.ClientDetailConfig.urls.downloadDocument;

            form.target = '_blank';

            form.style.display = 'none';



            // CSRF token

            const tokenElement = document.querySelector('meta[name="csrf-token"]');

            if (!tokenElement) {

                console.error('CSRF token not found');

                alert('Security token not found. Please refresh the page and try again.');

                target.innerHTML = originalText;

                target.style.pointerEvents = 'auto';

                return;

            }

            

            const token = tokenElement.getAttribute('content');

            

            // Add form fields

            const tokenInput = document.createElement('input');

            tokenInput.type = 'hidden';

            tokenInput.name = '_token';

            tokenInput.value = token;

            form.appendChild(tokenInput);



            const filelinkInput = document.createElement('input');

            filelinkInput.type = 'hidden';

            filelinkInput.name = 'filelink';

            filelinkInput.value = filelink;

            form.appendChild(filelinkInput);



            const filenameInput = document.createElement('input');

            filenameInput.type = 'hidden';

            filenameInput.name = 'filename';

            filenameInput.value = filename;

            form.appendChild(filenameInput);



          



            // Append form to body and submit

            document.body.appendChild(form);

            

            try {

                form.submit();

                

                // Reset button after a short delay

                setTimeout(function() {

                    target.innerHTML = originalText;

                    target.style.pointerEvents = 'auto';

                }, 2000);

                

            } catch (error) {

                console.error('Error submitting form:', error);

                alert('Error initiating download. Please try again.');

                target.innerHTML = originalText;

                target.style.pointerEvents = 'auto';

            }



            // Remove form after submission

            setTimeout(function() {

                if (form.parentNode) {

                    form.parentNode.removeChild(form);

                }

            }, 1000);

        });

    });





    //JavaScript to Show File Selection Hint

    document.addEventListener('DOMContentLoaded', function() {

        // Trigger file input click when "Add Document" button is clicked

        document.querySelector('.add-document-btn').addEventListener('click', function() {

            document.querySelector('.docclientreceiptupload').click();

        });



        // Show file selection hint when files are selected

        document.querySelector('.docclientreceiptupload').addEventListener('change', function(e) {

            const files = e.target.files;

            const hintElement = document.querySelector('.file-selection-hint');



            if (files.length > 0) {

                if (files.length === 1) {

                    // Show the file name if only one file is selected

                    hintElement.textContent = `${files[0].name} selected`;

                } else {

                    // Show the number of files if multiple files are selected

                    hintElement.textContent = `${files.length} Files selected`;

                }

            } else {

                // Clear the hint if no files are selected

                hintElement.textContent = '';

            }

        });





        // Trigger file input click when "Add Document" button is clicked

        document.querySelector('.add-document-btn1').addEventListener('click', function() {

            document.querySelector('.docofficereceiptupload').click();

        });



        // Show file selection hint when files are selected

        document.querySelector('.docofficereceiptupload').addEventListener('change', function(e) {

            const files = e.target.files;

            const hintElement1 = document.querySelector('.file-selection-hint1');



            if (files.length > 0) {

                if (files.length === 1) {

                    // Show the file name if only one file is selected

                    hintElement1.textContent = `${files[0].name} selected`;

                } else {

                    // Show the number of files if multiple files are selected

                    hintElement1.textContent = `${files.length} Files selected`;

                }

            } else {

                // Clear the hint if no files are selected

                hintElement1.textContent = '';

            }

        });

    });



    document.addEventListener('DOMContentLoaded', function () {

        const radios = document.querySelectorAll('input[name="receipt_type"]');

        const forms = document.querySelectorAll('.form-type');



        radios.forEach(radio => {

            radio.addEventListener('change', function () {

                forms.forEach(form => form.style.display = 'none');

                const selected = this.value; //alert(selected);

                document.getElementById(selected + '_form').style.display = 'block';

                //var selectedMatter = $('#sel_matter_id_client_detail').val();

                let selectedMatter;

                if ($('.general_matter_checkbox_client_detail').is(':checked')) {

                    // If checkbox is checked, get its value

                    selectedMatter = $('.general_matter_checkbox_client_detail').val();

                    //console.log('Checkbox is checked, selected value:', selectedMatter);

                } else {

                    // If checkbox is not checked, get the value from the dropdown

                    selectedMatter = $('#sel_matter_id_client_detail').val();

                    //console.log('Checkbox is not checked, selected dropdown value:', selectedMatter);

                }

                //console.log('selectedMatter==='+selectedMatter);

                if(selected == 'office_receipt'){

                    listOfInvoice();

                    $('#client_matter_id_office').val(selectedMatter);

                }

                else if(selected == 'invoice_receipt'){

                    //alert('function_type=='+ $('#function_type').val() )

                    if($('#function_type').val() == '' || $('#function_type').val() == 'add' ) {

                        $('#function_type').val("add");

                        getTopInvoiceNoFromDB(3);

                    }

                    $('#client_matter_id_invoice').val(selectedMatter);

                }

                else if(selected == 'client_receipt'){

                    listOfInvoice();

                    clientLedgerBalanceAmount(selectedMatter);

                    $('#client_matter_id_ledger').val(selectedMatter);

                }

            });

        });

    });



    function getTopInvoiceNoFromDB(type) {

        $.ajax({

            type:'post',

            url: window.ClientDetailConfig.urls.getTopInvoiceNo,

            sync:true,

            data: {type:type},

            success: function(response){

                var obj = $.parseJSON(response); 

                $('.invoice_no').val(obj.max_receipt_id);

                $('.unique_invoice_no').text(obj.max_receipt_id);

            }

        });

    }



    function getTopReceiptValInDB(type) {

        $.ajax({

            type:'post',

            url: window.ClientDetailConfig.urls.getTopReceiptVal,

            sync:true,

            data: {type:type},

            success: function(response){

                var obj = $.parseJSON(response);

                if(obj.receipt_type == 1){ //client receipt

                    if(obj.record_count >0){

                        $('#top_value_db').val(obj.record_count);

                    } else {

                        $('#top_value_db').val(obj.record_count);

                    }

                }



                if(obj.receipt_type == 2){ //office receipt

                    if(obj.record_count >0){

                        $('#office_top_value_db').val(obj.record_count);

                    } else {

                        $('#office_top_value_db').val(obj.record_count);

                    }

                }



                if(obj.receipt_type == 4){ //journal receipt

                    if(obj.record_count >0){

                        $('#journal_top_value_db').val(obj.record_count);

                    } else {

                        $('#journal_top_value_db').val(obj.record_count);

                    }

                }



                if(obj.receipt_type == 3){ //invoice receipt

                    if(obj.record_count >0){

                        $('#invoice_top_value_db').val(obj.record_count);

                    } else {

                        $('#invoice_top_value_db').val(obj.record_count);

                    }



                    if(obj.max_receipt_id >0){

                        var max_receipt_id = obj.max_receipt_id +1;

                        max_receipt_id = "Inv000"+max_receipt_id;

                        $('.unique_invoice_no').text(max_receipt_id);

                        $('.invoice_no').val(max_receipt_id);

                    } else {

                        var max_receipt_id = obj.max_receipt_id +1;

                        max_receipt_id = "Inv000"+max_receipt_id;

                        $('.unique_invoice_no').text(max_receipt_id);

                        $('.invoice_no').val(max_receipt_id);

                    }

                }

            }

        });

    }



    //List of invoice values for drop down

    function listOfInvoice() {

        var client_id = window.ClientDetailConfig.clientId;

        let selectedMatter;

        if ($('.general_matter_checkbox_client_detail').is(':checked')) {

            selectedMatter = $('.general_matter_checkbox_client_detail').val();

        } else {

            selectedMatter = $('#sel_matter_id_client_detail').val();

        }

        

        $.ajax({

            type:'post',

            url: window.ClientDetailConfig.urls.listOfInvoice,

            sync:true,

            data: { client_id:client_id, selectedMatter:selectedMatter},

            success: function(response){

                var obj = $.parseJSON(response);

                $('.invoice_no_cls').html(obj.record_get);

            }

        });

    }



    function clientLedgerBalanceAmount(selectedMatter) {

        var client_id = window.ClientDetailConfig.clientId;

        $.ajax({

            type:'post',

            url: window.ClientDetailConfig.urls.clientLedgerBalance,

            sync:true,

            data: { client_id:client_id , selectedMatter:selectedMatter },

            success: function(response){

                var obj = $.parseJSON(response);

                $('#client_ledger_balance_amount').val(obj.record_get);

            }

        });

    }



    function downloadFile(url, fileName) {

        // Create a temporary anchor element

        const link = document.createElement('a');

        link.href = url;

        link.download = fileName; // Set the desired file name

        document.body.appendChild(link);

        link.click();

        document.body.removeChild(link); // Clean up

    }

$(document).ready(function() {

    

    









    //Send message

    $(document).delegate('.sendmsg', 'click', function(){

        $('#sendmsgmodal').modal('show');

        var client_id = $(this).attr('data-id');

        $('#sendmsg_client_id').val(client_id);

    });



    // Initialize Sidebar Tabs Management

    $(document).ready(function() {

        if (typeof SidebarTabs !== 'undefined' && window.ClientDetailConfig) {

            SidebarTabs.init({

                clientId: window.ClientDetailConfig.encodeId,

                matterId: window.ClientDetailConfig.matterId,

                activeTab: window.ClientDetailConfig.activeTab,

                selectedMatter: ''

            });

        } else {

            console.error('[DetailMain] SidebarTabs or ClientDetailConfig not available');

        }

    });

    

    

    // REMOVED: Duplicate popstate handler - now handled by sidebar-tabs.js



    //Handle Client Portal tab click specifically

    function showClientMatterApplicationData(selectedMatter){

        // Get client_id from the current page

        var clientId = window.ClientDetailConfig.clientId;

        

        // Show loading message in the application tab

        $('#application-tab').html('<h4>Please wait, upserting application record...</h4>');

        

        // Step 1: Insert/Update record in applications table

        $.ajax({

            url: window.ClientDetailConfig.urls.loadApplicationInsertUpdate,

            type: 'POST',

            data: {

                client_id: clientId,

                client_matter_id: selectedMatter

            },

            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            },

            success: function(upsertResponse) {

                if(upsertResponse.status && upsertResponse.application_id) {

                    // Update loading message

                    $('#application-tab').html('<h4>Please wait, loading application details...</h4>');

                    var appliid = upsertResponse.application_id;

                    // Step 2: Call getapplicationdetail route with the application_id from upsert response

                    $.ajax({

                        url: window.ClientDetailConfig.urls.getApplicationDetail,

                        type:'GET',

                        data:{id: appliid},

                        success:function(response){

                            // Display the response directly in the application tab

                            $('#application-tab').html(response);

                            

                            $('.popuploader').hide();

                            // Render only inside the Application tab to avoid leaking into Personal Details

                            $('#application-tab').html(response);

                            $('.datepicker').daterangepicker({

                                locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },

                                singleDatePicker: true,

                                showDropdowns: true,

                            }, function(start, end, label) {

                                $('#popuploader').show();

                                $.ajax({

                                    url: window.ClientDetailConfig.urls.updateIntake,

                                    method: "GET", // or POST

                                    dataType: "json",

                                    data: {from: start.format('YYYY-MM-DD'), appid: appliid},

                                    success:function(result) {

                                        $('#popuploader').hide();

                                        console.log("sent back -> do whatever you want now");

                                    }

                                });

                            });



                            $('.expectdatepicker').daterangepicker({

                            locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },

                                            singleDatePicker: true,



                                            showDropdowns: true,

                            }, function(start, end, label) {

                                $('#popuploader').show();

                                $.ajax({

                                    url: window.ClientDetailConfig.urls.updateExpectWin,

                                    method: "GET", // or POST

                                    dataType: "json",

                                    data: {from: start.format('YYYY-MM-DD'), appid: appliid},

                                    success:function(result) {

                                        $('#popuploader').hide();



                                    }

                                });

                            });



                            $('.startdatepicker').daterangepicker({

                            locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },

                                            singleDatePicker: true,



                                            showDropdowns: true,

                            }, function(start, end, label) {

                                $('#popuploader').show();

                                $.ajax({

                                    url: window.ClientDetailConfig.urls.updateDates,

                                    method: "GET", // or POST

                                    dataType: "json",

                                    data: {from: start.format('YYYY-MM-DD'), appid: appliid, datetype: 'start'},

                                    success:function(result) {

                                        $('#popuploader').hide();

                                        var obj = result;

                                        if(obj.status){

                                            $('.app_start_date .month').html(obj.dates.month);

                                            $('.app_start_date .day').html(obj.dates.date);

                                            $('.app_start_date .year').html(obj.dates.year);

                                        }

                                        console.log("sent back -> do whatever you want now");

                                    }

                                });

                            });



                            $('.enddatepicker').daterangepicker({

                            locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },

                                            singleDatePicker: true,



                                            showDropdowns: true,

                            }, function(start, end, label) {

                                $('#popuploader').show();

                                $.ajax({

                                    url: window.ClientDetailConfig.urls.updateDates,

                                    method: "GET", // or POST

                                    dataType: "json",

                                    data: {from: start.format('YYYY-MM-DD'), appid: appliid, datetype: 'end'},

                                    success:function(result) {

                                        $('#popuploader').hide();

                                        var obj = result;

                                        if(obj.status){

                                            $('.app_end_date .month').html(obj.dates.month);

                                            $('.app_end_date .day').html(obj.dates.date);

                                            $('.app_end_date .year').html(obj.dates.year);

                                        }

                                        console.log("sent back -> do whatever you want now");

                                    }

                                });

                            });





                          

                        },

                        error: function(xhr, status, error) {

                            console.error('Error loading application details:', error);

                            $('#application-tab').html('<h4>Error loading application details. Please try again.</h4>');

                        }

                    });

                } else {

                    $('#application-tab').html('<h4>Error upserting application record. Please try again.</h4>');

                }

            },

            error: function(xhr, status, error) {

                console.error('Error upserting application:', error);

                $('#application-tab').html('<h4>Error upserting application record. Please try again.</h4>');

            }

        });

    }



   // Assuming this is part of the rendering logic for Client Funds Ledger entries

    function renderClientFundsLedger(entries) {

        var trRows = "";

        $.each(entries, function(index, entry) {

            let typeIconMap = {

                'Deposit': 'fa-arrow-down',

                'Fee Transfer': 'fa-arrow-right-from-bracket',

                'Disbursement': 'fa-arrow-right-from-bracket',

                'Refund': 'fa-arrow-right-from-bracket'

            };

            let typeIcon = typeIconMap[entry.client_fund_ledger_type] || 'fa-money-bill';

            let typeClass = entry.client_fund_ledger_type === 'Deposit' ? 'text-success' : 'text-primary';



            let depositAmount = entry.deposit_amount ? '$' + parseFloat(entry.deposit_amount).toFixed(2) : '$0.00';

            let withdrawAmount = entry.withdraw_amount ? '$' + parseFloat(entry.withdraw_amount).toFixed(2) : '$0.00';

            let balanceAmount = entry.balance_amount ? '$' + parseFloat(entry.balance_amount).toFixed(2) : '$0.00';



            // Add pencil icon for non-Fee Transfer entries

            let editIcon = entry.client_fund_ledger_type !== 'Fee Transfer' ?

                `<a href="#" class="edit-ledger-entry" data-id="${entry.id}" data-trans-date="${entry.trans_date}" data-entry-date="${entry.entry_date}" data-type="${entry.client_fund_ledger_type}" data-description="${entry.description}" data-deposit="${entry.deposit_amount}" data-withdraw="${entry.withdraw_amount}"><i class="fas fa-pencil-alt"></i></a>` : '';



            trRows += `

                <tr data-id="${entry.id}">

                    <td>${entry.trans_date} ${editIcon}</td>

                    <td class="type-cell">

                        <i class="fas ${typeIcon} type-icon ${typeClass}"></i>

                        <span>${entry.client_fund_ledger_type}${entry.invoice_no ? '<br/>(' + entry.invoice_no + ')' : ''}</span>

                    </td>

                    <td class="description">${entry.description}</td>

                    <td><a href="#" title="View Receipt ${entry.trans_no}">${entry.trans_no}</a></td>

                    <td class="currency text-success">${depositAmount}</td>

                    <td class="currency text-danger">${withdrawAmount}</td>

                    <td class="currency">${balanceAmount}</td>

                </tr>

            `;

        });



        $('.client-funds-ledger-list').html(trRows);

    }



    // Handle pencil icon click to open modal

    $(document).on('click', '.edit-ledger-entry', function(e) {

        e.preventDefault();

        var $row = $(this).closest('tr');

        var id = $(this).data('id');

        var transDate = $(this).data('trans-date');

        var entryDate = $(this).data('entry-date');

        var type = $(this).data('type');

        var description = $(this).data('description');

        var deposit = $(this).data('deposit');

        var withdraw = $(this).data('withdraw');



        // Populate modal fields

        $('#editLedgerModal input[name="id"]').val(id);

        $('#editLedgerModal input[name="trans_date"]').val(transDate);

        $('#editLedgerModal input[name="entry_date"]').val(entryDate);

        $('#editLedgerModal input[name="client_fund_ledger_type"]').val(type).prop('readonly', true); // Make type readonly

        $('#editLedgerModal input[name="description"]').val(description);



        // Handle Funds In and Funds Out - disable if zero

        if (parseFloat(deposit) === 0) {

            $('#editLedgerModal input[name="deposit_amount"]').val(deposit).prop('readonly', true);

        } else {

            $('#editLedgerModal input[name="deposit_amount"]').val(deposit).prop('readonly', false);

        }



        if (parseFloat(withdraw) === 0) {

            $('#editLedgerModal input[name="withdraw_amount"]').val(withdraw).prop('readonly', true);

        } else {

            $('#editLedgerModal input[name="withdraw_amount"]').val(withdraw).prop('readonly', false);

        }



        // Initialize datepickers

        $('#editLedgerModal input[name="trans_date"]').datepicker({

            format: 'dd/mm/yyyy',

            autoclose: true,

            todayHighlight: true

        });



        $('#editLedgerModal input[name="entry_date"]').datepicker({

            format: 'dd/mm/yyyy',

            autoclose: true,

            todayHighlight: true

        });



        // Show modal

        $('#editLedgerModal').modal('show');

    });





    // Handle update button click in modal

    $('#updateLedgerEntryBtn').on('click', function() {

        var form = $('#editLedgerForm')[0];

        var formData = new FormData(form); // Use FormData to include file uploads



        $.ajax({

            type: 'POST',

            url: window.ClientDetailConfig.urls.updateClientFundsLedger,

            data: formData,

            processData: false, // Prevent jQuery from processing the data

            contentType: false, // Let the browser set the content type for multipart/form-data

            success: function(response) {

                if (response.status) {

                    $('#editLedgerModal').modal('hide');

                    localStorage.setItem('activeTab', 'accounts');

                    location.reload();

                    $('.custom-error-msg').html('<span class="alert alert-success">' + response.message + '</span>');



                    // Update the Client Funds Ledger table

                    renderClientFundsLedger(response.updatedEntries);



                    // Update Current Funds Held

                    $('.current-funds-held').text('$ ' + parseFloat(response.currentFundsHeld).toFixed(2));

                } else {

                    $('.custom-error-msg').html('<span class="alert alert-danger">' + response.message + '</span>');

                }

            },

            error: function(xhr, status, error) {

                $('.custom-error-msg').html('<span class="alert alert-danger">An error occurred. Please try again.</span>');

                console.error('AJAX error:', status, error);

            }

        });

    });



    // Handle document subtab switching

    $('.subtab-button').click(function() {

        // Remove active class from all document subtab buttons and panes

        $('.subtab-button').removeClass('active');

        $('.subtab-pane').removeClass('active');



        // Add active class to clicked button

        $(this).addClass('active');



        // Show corresponding pane

        const subtabId = $(this).data('subtab');

        $(`#${subtabId}-subtab`).addClass('active');



        if ($('.general_matter_checkbox_client_detail').is(':checked')) {

            var selectedMatter = $('.general_matter_checkbox_client_detail').val();

        } else {

            var selectedMatter = $('#sel_matter_id_client_detail').val();

        }



       



        if( subtabId == 'inbox') {

            if(selectedMatter != "" ) {

                $('#inbox-subtab #email-list').find('.email-card').each(function() {

                    if ($(this).data('matterid') == selectedMatter) {

                        $(this).show();

                    } else {

                        $(this).hide();

                    }

                });

            }  else {

                $(this).hide();

            }

        }



        if( subtabId == 'sent') {

            if(selectedMatter != "" ) {

                $('#sent-subtab #email-list1').find('.email-card').each(function() {

                    if ($(this).data('matterid') == selectedMatter) {

                        $(this).show();

                    } else {

                        $(this).hide();

                    }

                });

            }  else {

                $(this).hide();

            }

        }



        //alert(subtabId);

        if( subtabId == 'migrationdocuments') {

            //var selectedMatter = $('#sel_matter_id_client_detail').val();

            //console.log('selectedMatter&&&&==='+selectedMatter);

            if(selectedMatter != "" ) {

                $('#migrationdocuments-subtab .migdocumnetlist1').find('.drow').each(function() {

                    if ($(this).data('matterid') == selectedMatter) {

                        $(this).show();

                    } else {

                        $(this).hide();

                    }

                });

            }  else {

                $(this).hide();

            }

        }







        // Store the active tab in localStorage when a tab is clicked

        localStorage.setItem('subactiveTab', subtabId);

    });



    // On page load, check localStorage and activate the correct tab

    const subactiveTab = localStorage.getItem('subactiveTab');

    if (subactiveTab) {

        // Find the button corresponding to the stored tabId and trigger its click event

        const $subtargetButton = $(`.subtab-button[data-tab="${subactiveTab}"]`);

        if ($subtargetButton.length) {

            $subtargetButton.click(); // Trigger the click event to reuse the existing logic

        }

        // Clear localStorage to prevent persistence on future loads (optional)

        localStorage.removeItem('subactiveTab');

    }



    //Notes subtab click

    $('.subtab8-button').click(function(e) {

        e.preventDefault();



        // Remove active class from all buttons and panes

        $('.subtab8-button').removeClass('active');

        $('.subtab8-pane').removeClass('active');



        // Add active class to clicked button

        $(this).addClass('active');



        // Show corresponding pane

        const subtabId8 = $(this).data('subtab8'); //alert(subtabId8);

        $(`#${subtabId8}-subtab8`).addClass('active');

    });



    //Document subtab like - Personal

    $('.subtab2-button').click(function(e) {

        e.preventDefault();



        // Remove active class from all buttons and panes

        $('.subtab2-button').removeClass('active');

        $('.subtab2-pane').removeClass('active');



        // Add active class to clicked button

        $(this).addClass('active');



        // Show corresponding pane

        const subtabId2 = $(this).data('subtab2'); //alert(subtabId2);

        $(`#${subtabId2}-subtab2`).addClass('active');

    });



    //Document subtab like - Visa

    $('.subtab6-button').click(function(e) {

        e.preventDefault();



        // Remove active class from all buttons and panes

        $('.subtab6-button').removeClass('active');

        $('.subtab6-pane').removeClass('active');



        // Add active class to clicked button

        $(this).addClass('active');



        // Show corresponding pane

        const subtabId6 = $(this).data('subtab6'); //alert(subtabId6);

        $(`#${subtabId6}-subtab6`).addClass('active');

    });





    // Handle form generation subtab switching

    $('.subtab3-button').click(function() {

        // Remove active class from all document subtab buttons and panes

        $('.subtab3-button').removeClass('active');

        $('.subtab3-pane').removeClass('active');



        // Add active class to clicked button

        $(this).addClass('active');



        // Show corresponding pane

        const subtabId = $(this).data('subtab');

        $(`#${subtabId}-subtab`).addClass('active');



        if ($('.general_matter_checkbox_client_detail').is(':checked')) {

            var selectedMatter = $('.general_matter_checkbox_client_detail').val();

        } else {

            var selectedMatter = $('#sel_matter_id_client_detail').val();

        }



		if( subtabId == 'form956') {

            if(selectedMatter != "" ) {

                $('#form956-subtab #form-list').find('.form-card').each(function() {

                    if ($(this).data('matterid') == selectedMatter) {

                        $(this).show();

                    } else {

                        $(this).hide();

                    }

                });

            }  else {

                $(this).hide();

            }

        }



        if( subtabId == 'costform') {

            if(selectedMatter != "" ) {

                $('#costform-subtab #form-list1').find('.form-card').each(function() {

                    if ($(this).data('matterid') == selectedMatter) {

                        $(this).show();

                    } else {

                        $(this).hide();

                    }

                });

            }  else {

                $(this).hide();

            }

        }



		/*if( subtabId == 'visaagreementform') {

            if(selectedMatter != "" ) {

                $('#visaagreementform-subtab #form-list2').find('.form-card').each(function() {

                    if ($(this).data('matterid') == selectedMatter) {

                        $(this).show();

                    } else {

                        $(this).hide();

                    }

                });

            }  else {

                $(this).hide();

            }

        }*/





        if( subtabId == 'costformL') {

            if(selectedMatter != "" ) {

                $('#costformL-subtab #form-list1').find('.form-card').each(function() {

                    if ($(this).data('matterid') == selectedMatter) {

                        $(this).show();

                    } else {

                        $(this).hide();

                    }

                });

            }  else {

                $(this).hide();

            }

        }

    });



    // Handle Filter by Status and Search Communication

    $('#filter-status, #search-communication').on('change keyup', function() {

        var status = $('#filter-status').val();

        var search = $('#search-communication').val();



        $.ajax({

            url: window.ClientDetailConfig.urls.filterEmails,

            type: 'POST',

            data: {

                client_id: window.ClientDetailConfig.clientId,

                status: status,

                search: search,

                _token: $('meta[name="csrf-token"]').attr('content')

            },

            success: function(response) {

                try {

                    // Parse the response as JSON

                    var emails = typeof response === 'string' ? JSON.parse(response) : response;



                    // Check if the response indicates an error

                    if (emails.status === 'error') {

                        $('#email-list').html('<p>' + emails.message + '</p>');

                        return;

                    }



                    var emailList = $('#email-list');

                    emailList.empty(); // Clear current emails



                    if (emails.length > 0) {

                        emails.forEach(function(email) {

                            var emailCard = `

                                <div class="email-card" data-email-id="${email.id}">

                                    <div class="email-meta">

                                        <span class="author-initial">${email.from_mail ? email.from_mail.charAt(0) : 'N/A'}</span>

                                        <div class="email-info">

                                            <span class="author-name">${email.from_mail || 'Unknown'}</span>

                                            <span class="email-timestamp">${new Date(email.created_at).toLocaleString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) || 'N/A'}</span>

                                        </div>

                                    </div>

                                    <div class="email-body">

                                        <h4>${email.subject || 'No Subject'}</h4>

                                        <p>To: ${email.to_mail || 'Unknown'}</p>

                                    </div>

                                    <div class="email-actions">



                                        ${email.preview_url ? `<a href="${email.preview_url}" class="btn btn-link mail_preview_modal" memail_id="${email.id}" target="_blank">Preview</a>` : '<span>No Preview Available</span>'}

                                        <button class="btn btn-link create_note" datamailid="${email.id}" datasubject="${email.subject}" datatype="mailnote">Create Note</button>

					                    <button class="btn btn-link inbox_reassignemail_modal" memail_id="${email.id}" user_mail="${email.to_mail}" uploaded_doc_id="${email.uploaded_doc_id}" href="javascript:;">Reassign</button>

                                    </div>

                                </div>`;

                            emailList.append(emailCard);

                        });

                    } else {

                        emailList.append('<p>No emails found.</p>');

                    }

                } catch (e) {

                    console.error('Error parsing JSON response:', e);

                    $('#email-list').html('<p>Error parsing email data. Please try again.</p>');

                }

            },

            error: function(xhr, status, error) {

                console.error('AJAX error:', status, error);

                $('#email-list').html('<p>Error fetching emails. Please try again. Status: ' + status + '</p>');

            }

        });

    });





    // Handle Filter by type ,status and Search Communication

    $('#filter-type1, #filter-status1, #search-communication1').on('change keyup', function() {

        const type = $('#filter-type1').val();

        const status = $('#filter-status1').val();

        const search = $('#search-communication1').val();

        const clientId = window.ClientDetailConfig.clientId;

        const emailList = $('#email-list1');



        $.ajax({

            url: window.ClientDetailConfig.urls.base + '/clients/filter/sentmails',

            type: 'POST',

            data: {

                client_id: clientId,

                type: type,

                status: status,

                search: search,

                _token: $('meta[name="csrf-token"]').attr('content')

            },

            beforeSend: function() {

                emailList.html('<p>Loading...</p>');

            },

            success: function(response) {

                emailList.empty();



                if (response.status === 'error') {

                    emailList.html(`<p class="text-danger">${response.message}</p>`);

                    return;

                }



                if (!Array.isArray(response) || response.length === 0) {

                    emailList.append('<p>No emails found.</p>');

                    return;

                }



                response.forEach(email => {

                    // Sanitize and format data

                    const authorInitial = email.from_mail ? email.from_mail.charAt(0).toUpperCase() : 'N/A';

                    const timestamp = email.created_at

                        ? new Date(email.created_at).toLocaleString('en-GB', {

                            day: '2-digit',

                            month: '2-digit',

                            year: 'numeric',

                            hour: '2-digit',

                            minute: '2-digit'

                        })

                        : email.fetch_mail_sent_time || 'N/A';

                    const typeBadge = email.conversion_type

                        ? '<span class="badge badge-success">Assigned</span>'

                        : '<span class="badge badge-warning">Delivered</span>';

                    const previewLink = email.preview_url

                        ? `<a href="${email.preview_url}" class="btn btn-link mail_preview_modal" memail_id="${email.id}" target="_blank"><i class="fas fa-eye"></i> Preview</a>`

                        : `<a class="btn btn-link sent_mail_preview_modal" memail_message="${email.message || ''}" memail_subject="${email.subject || ''}"><i class="fas fa-eye"></i> Preview Mail</a>`;

                    const reassignButton = email.conversion_type

                        ? `<button class="btn btn-link sent_reassignemail_modal" memail_id="${email.id}" user_mail="${email.to_mail || ''}" uploaded_doc_id="${email.uploaded_doc_id || ''}">Reassign</button>`

                        : '';



                    const emailCard = `

                        <div class="email-card" data-email-id="${email.id}">

                            <div class="email-meta">

                                <span class="author-initial">${authorInitial}</span>

                                <div class="email-info">

                                    <span class="author-name">Sent by: <strong>${email.from_mail || 'Unknown'}</strong> ${typeBadge}</span>

                                    <span class="email-timestamp">${timestamp}</span>

                                </div>

                            </div>

                            <div class="email-body">

                                <h4>${email.subject || 'No Subject'}</h4>

                                <p>Sent To: ${email.to_mail || 'Unknown'}</p>

                            </div>

                            <div class="email-actions">

                                ${previewLink}

                                <button class="btn btn-link create_note" datamailid="${email.id}" datasubject="${email.subject || ''}" datatype="mailnote"><i class="fas fa-sticky-note"></i> Create Note</button>

                                ${reassignButton}

                            </div>

                        </div>`;

                    emailList.append(emailCard);

                });

            },

            error: function(xhr, status, error) {

                console.error('AJAX Error:', status, error, xhr.responseText);

                emailList.html('<p class="text-danger">Error fetching emails. Please try again later.</p>');

            }

        });

    });





    // Initialize Activity Feed visibility on page load

    if ($('#personaldetails-tab').hasClass('active')) {

        $('#activity-feed').show();

        $('#main-content').css('flex', '1');

        

        // Adjust Activity Feed height on initial load

        setTimeout(function() {

            adjustActivityFeedHeight();

        }, 150);

    } else {

        $('#activity-feed').hide();

        //$('#main-content').css('flex', '0 0 100%');

    }

});



    function previewFile(fileType, fileUrl, containerId) {

        //console.log('fileType='+fileType);

        //console.log('fileUrl='+fileUrl);

        //console.log('containerId='+containerId);

        const container = $(`.${containerId}`);



        // Show loading state

        container.html(`

            <div class="preview-content" style="flex: 1; display: flex; align-items: center; justify-content: center;">

                <div style="text-align: center;">

                    <i class="fas fa-spinner fa-spin fa-2x" style="color: #4a90e2;"></i>

                    <p style="margin-top: 10px; color: #666;">Loading preview...</p>

                </div>

            </div>

        `);



        // Determine content based on file type

        let content = '';



        if (fileType.toLowerCase().match(/(jpg|jpeg|png|gif)$/)) {

            const img = new Image();

            img.onload = function() {

                container.html(`

                    <div class="preview-content" style="flex: 1; overflow: auto; text-align: center;">

                        <img src="${fileUrl}" alt="Document Preview" style="max-width: 100%; max-height: calc(100vh - 300px); margin: auto; display: block;" />

                    </div>

                `);

            };

            img.src = fileUrl;

        } else if (fileType.toLowerCase() === 'pdf') {

            container.html(`

                <div class="preview-content" style="flex: 1; overflow: hidden;width: 475px !important;">

                    <iframe src="${fileUrl}" type="application/pdf" style="width: 100%; height: calc(100vh - 100px); border: none;"></iframe>

                </div>

            `);

        }

        else if (fileType.toLowerCase().match(/^(docx?|xlsx?|pptx?)$/)) {

            const officeViewerUrl = `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(fileUrl)}`;

            container.html(`

                <div class="preview-content" style="flex: 1; overflow: hidden; width: 100%;">

                    <iframe src="${officeViewerUrl}" class="doc-viewer" style="width: 100%; height: calc(100vh - 100px); border: none;"></iframe>

                </div>

            `);

        }

        else {

            container.html(`

                <div class="preview-content" style="flex: 1; display: flex; align-items: center; justify-content: center; flex-direction: column;">

                    <i class="fas fa-file fa-3x" style="color: #6c757d; margin-bottom: 15px;"></i>

                    <p style="margin-bottom: 15px;">Preview not available for this file type.</p>

                    <a href="${fileUrl}" target="_blank" class="btn btn-primary">Open File</a>

                </div>

            `);

        }

    }



    // Update preview container styles when the document is ready

    $(document).ready(function() {

        // Style all preview containers

        $('.preview-pane.file-preview-container').css({

            'display': 'flex',

            'flex-direction': 'column',

            'margin-top': '15px',

            'width': '499px',

            'min-height': '500px',

            'height': 'calc(100vh - 200px)',

            'border': '1px solid #dee2e6',

            'border-radius': '4px',

            'padding': '15px',

            'background': '#fff',

            'position': 'sticky',

            'top': '20px'

        });



        // Handle window resize

        $(window).resize(function() {

            adjustPreviewContainers();

        }).resize(); // Trigger on load

    });



    // Function to adjust preview containers

    function adjustPreviewContainers() {

        $('.preview-pane.file-preview-container').each(function() {

            const windowHeight = $(window).height();

            const containerTop = $(this).offset().top;

            const desiredHeight = windowHeight - containerTop - 50; // 50px buffer



            if (desiredHeight >= 600) { // Minimum height

                $(this).css('height', desiredHeight + 'px');

            } else {

                $(this).css('height', '600px');

            }

        });

    }







    jQuery(document).ready(function($){



        // Initialize Select2 for the matter dropdown

        $('#sel_matter_id_client_detail').select2();



        $('.selecttemplate').select2({dropdownParent: $('#emailmodal')});



        //mail preview click update mail_is_read bit

        $('.mail_preview_modal').on('click', function(){

            var mail_report_id = $(this).attr('memail_id');

            $.ajaxSetup({

                headers: {

                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                }

            });

            $.ajax({

                url: window.ClientDetailConfig.urls.base + '/clients/updatemailreadbit',

                method: "POST",

                data: {mail_report_id:mail_report_id},

                datatype: 'json',

                success: function(response) {

                }

            });

        });



        //inbox mail reassign Model popup code start

        $(document).on('click', '.inbox_reassignemail_modal', function() {

            var val = $(this).attr('memail_id');

            $('#inbox_reassignemail_modal #memail_id').val(val);

            var user_mail = $(this).attr('user_mail');

            $('#inbox_reassignemail_modal #user_mail').val(user_mail);

            var uploaded_doc_id = $(this).attr('uploaded_doc_id');

            $('#inbox_reassignemail_modal #uploaded_doc_id').val(uploaded_doc_id);

            $('#inbox_reassignemail_modal').modal('show');

        });



        //Initialize both Select2 dropdowns

        $('#reassign_client_id').select2();

        $('#reassign_client_matter_id').select2();



        $(document).delegate('#reassign_client_id', 'change', function(){

            let selected_client_id = $(this).val();

            



            if (selected_client_id != "") {

                $('.popuploader').show();

                $.ajaxSetup({

                    headers: {

                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                    }

                });

                $.ajax({

                    url: window.ClientDetailConfig.urls.base + '/clients/listAllMattersWRTSelClient',

                    method: "POST",

                    data: {client_id:selected_client_id},

                    datatype: 'json',

                    success: function(response) {

                        $('.popuploader').hide();

                        var obj = $.parseJSON(response); 

                        var matterlist = '<option value="">Select Client Matter</option>';

                        $.each(obj.clientMatetrs, function(index, subArray) {

                            matterlist += '<option value="'+subArray.id+'">'+subArray.title+'('+subArray.client_unique_matter_no+')</option>';

                        });

                        $('#reassign_client_matter_id').html(matterlist);

                    }

                });

                $('#reassign_client_matter_id').prop('disabled', false).select2();

            } else {

                $('#reassign_client_matter_id').prop('disabled', true).select2();

            }

        });





        //sent mail reassign Model popup code start

        $(document).on('click', '.sent_reassignemail_modal', function() {

            var val = $(this).attr('memail_id');

            $('#sent_reassignemail_modal #memail_id').val(val);

            var user_mail = $(this).attr('user_mail');

            $('#sent_reassignemail_modal #user_mail').val(user_mail);

            var uploaded_doc_id = $(this).attr('uploaded_doc_id');

            $('#sent_reassignemail_modal #uploaded_doc_id').val(uploaded_doc_id);

            $('#sent_reassignemail_modal').modal('show');

        });



        $('.sent_mail_preview_modal').on('click', function(){

            var memail_subject = $(this).attr('memail_subject');

            $('#sent_mail_preview_modal #memail_subject').html(memail_subject);



            var memail_message = $(this).attr('memail_message');

            $('#sent_mail_preview_modal #memail_message').html(memail_message);



            $('#sent_mail_preview_modal').modal('show');

        });



        //Initialize both Select2 dropdowns

        $('#reassign_sent_client_id').select2();

        $('#reassign_sent_client_matter_id').select2();



        $(document).delegate('#reassign_sent_client_id', 'change', function(){

            let selected_client_id = $(this).val();

            if (selected_client_id != "") {

                $('.popuploader').show();

                $.ajaxSetup({

                    headers: {

                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                    }

                });

                $.ajax({

                    url: window.ClientDetailConfig.urls.base + '/clients/listAllMattersWRTSelClient',

                    method: "POST",

                    data: {client_id:selected_client_id},

                    datatype: 'json',

                    success: function(response) {

                        $('.popuploader').hide();

                        var obj = $.parseJSON(response); 

                        var matterlist = '<option value="">Select Client Matter</option>';

                        $.each(obj.clientMatetrs, function(index, subArray) {

                            matterlist += '<option value="'+subArray.id+'">'+subArray.title+'('+subArray.client_unique_matter_no+')</option>';

                        });

                        $('#reassign_sent_client_matter_id').html(matterlist);

                    }

                });

                $('#reassign_sent_client_matter_id').prop('disabled', false).select2();

            } else {

                $('#reassign_sent_client_matter_id').prop('disabled', true).select2();

            }

        });















        // Handle click event on the action button

        $(document).delegate('.btn-assignuser, .btn-create-action', 'click', function(){

            // Get the value from the #note_description Summernote editor

            var note_description = $('#note_description').summernote('code');



            // Preserve formatting while cleaning HTML tags and entities

            var clean_note_description = note_description

                .replace(/<br\s*\/?>/gi, '\n')  // Convert <br> tags to line breaks

                .replace(/<p[^>]*>/gi, '\n')    // Convert <p> tags to line breaks

                .replace(/<\/p>/gi, '')         // Remove closing </p> tags

                .replace(/<div[^>]*>/gi, '\n')  // Convert <div> tags to line breaks

                .replace(/<\/div>/gi, '')       // Remove closing </div> tags

                .replace(/<strong[^>]*>/gi, '**')  // Convert <strong> to ** for bold

                .replace(/<\/strong>/gi, '**')     // Close bold with **

                .replace(/<b[^>]*>/gi, '**')       // Convert <b> to ** for bold

                .replace(/<\/b>/gi, '**')          // Close bold with **

                .replace(/<em[^>]*>/gi, '*')       // Convert <em> to * for italic

                .replace(/<\/em>/gi, '*')          // Close italic with *

                .replace(/<i[^>]*>/gi, '*')        // Convert <i> to * for italic

                .replace(/<\/i>/gi, '*')           // Close italic with *

                .replace(/<u[^>]*>/gi, '__')       // Convert <u> to __ for underline

                .replace(/<\/u>/gi, '__')          // Close underline with __

                .replace(/<[^>]*>/g, '')           // Strip all remaining HTML tags

                .replace(/&nbsp;/g, ' ')           // Convert &nbsp; to regular spaces

                .replace(/&amp;/g, '&')            // Convert &amp; to &

                .replace(/&lt;/g, '<')             // Convert &lt; to <

                .replace(/&gt;/g, '>')             // Convert &gt; to >

                .replace(/&quot;/g, '"')           // Convert &quot; to "

                .replace(/&#39;/g, "'")            // Convert &#39; to '

                .replace(/\n\s*\n/g, '\n')         // Remove multiple consecutive line breaks

                .trim();                           // Remove leading/trailing whitespace



            // Display the clean value in an alert

            //alert(clean_note_description);



            // Transfer the original HTML content to the #assignnote field (preserving formatting)

            // If #assignnote is a Summernote editor, use summernote('code'), otherwise use val()

            if ($('#assignnote').hasClass('summernote-simple') || $('#assignnote').data('summernote')) {

                $('#assignnote').summernote('code', note_description);

            } else {

                $('#assignnote').val(clean_note_description);

            }



            // Close the #create_note_d modal

            $('#create_note_d').modal('hide');



            // Show the #create_action_popup modal

            $('#create_action_popup').modal('show');

        });







        // Toggle dropdown menu on button click

        $('.dropdown-toggle').on('click', function() {

            $(this).parent().toggleClass('show');

        });



        // Close the dropdown if clicked outside

        $(document).on('click', function(e) {

            if (!$(e.target).closest('.dropdown-multi-select').length) {

                $('.dropdown-multi-select').removeClass('show');

            }

        });



        // Handle checkbox click events

        $('.checkbox-item').on('change', function() {

            var selectedValues = [];



            // Collect selected checkboxes values

            $('.checkbox-item:checked').each(function() {

                selectedValues.push($(this).val());

            });



            // Set the selected values in the hidden select dropdown

            $('#rem_cat').val(selectedValues).trigger('change');

        });



        // Handle "Select All" functionality (If needed, you can include this part)

        $('#select-all').on('change', function() {

            if ($(this).is(':checked')) {

                // Select all checkboxes

                $('.checkbox-item').prop('checked', true).trigger('change');

            } else {

                // Deselect all checkboxes

                $('.checkbox-item').prop('checked', false).trigger('change');

            }

        });





        //Matter selection - unified dropdown approach

        var selectedMatter = '';



        //Note: General matter checkbox handlers removed - now using unified dropdown approach





        //Convert lead to client popup and select matter

        $(document).delegate('#general_matter_checkbox_new', 'change', function(){

            if (this.checked) {

                $('#sel_matter_id').prop('disabled', true).trigger('change');

                $('#sel_matter_id').removeAttr('data-valid').trigger('change');

            } else {

                $('#sel_matter_id').prop('disabled', false).trigger('change');

                $('#sel_matter_id').attr('data-valid', 'required').trigger('change');

            }

        });





        //Client detail page Select general matter checkbox and assign matter id

        $(document).delegate('.general_matter_checkbox_client_detail', 'change', function(){

            if (this.checked) {

                $('#sel_matter_id_client_detail').prop('disabled', true).trigger('change');

                $('#sel_matter_id_client_detail').removeAttr('data-valid').trigger('change');

                selectedMatter = $(this).val();

               



                var uniqueMatterNo = $(this).data('clientuniquematterno');

                

                // Get the active tab and sub tab

                var activeTab = $('.tab-button.active, .vertical-tab-button.active, .client-nav-button.active').data('tab') || 'personaldetails';

                var activeSubTab = $('.subtab-button.active').data('subtab');

                

            // Skip redirect during initialization

            if (isInitializing) {

                return;

            }

                

                // Build new URL

                var clientId = window.ClientDetailConfig.encodeId;

                var baseUrl = '/clients/detail/' + clientId;

                var currentUrl = window.location.href;



                var newUrl;

                if (selectedMatter != '' && uniqueMatterNo) {

                    // Append the new matter ID and active tab to the base URL

                    newUrl = baseUrl + '/' + uniqueMatterNo + '/' + activeTab;

                } else {

                    // If no matter is selected, redirect to the base URL with just the tab

                    newUrl = baseUrl + '/' + activeTab;

                }

                

                // Only redirect if the URL is actually changing to prevent infinite loops

                if (currentUrl.split('?')[0] !== newUrl && !currentUrl.endsWith(newUrl)) {

                    window.location.href = newUrl;

                    return; // Exit early to prevent further execution

                }



                if( activeTab == 'noteterm' ) {

                    // Apply combined matter and task group filtering
                    const activeTaskGroup = $('.subtab8-button.active').data('subtab8') || 'All';
                    
                    $('#noteterm-tab').find('.note-card-redesign').each(function() {
                        const noteMatterId = $(this).data('matterid');
                        const noteType = $(this).data('type');
                        
                        let showNote = false;
                        
                        // Matter filtering logic
                        if (selectedMatter !== "") {
                            // Show notes that match the selected matter OR notes with no matter_id
                            showNote = (noteMatterId == selectedMatter || noteMatterId == '' || noteMatterId == null);
                        } else {
                            // Show ALL notes when no matter is selected
                            showNote = true;
                        }
                        
                        // Task group filtering logic
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

                else if( activeTab == 'visadocuments') {

                    if(selectedMatter != "" ) {

                        $('#visadocuments-tab .migdocumnetlist1').find('.drow').each(function() {

                            if ($(this).data('matterid') == selectedMatter) {

                                $(this).show();

                            } else {

                                $(this).hide();

                            }

                        });

                    }  else {

                        $(this).hide();

                    }

                }



                else if( activeTab == 'conversations' && activeSubTab == 'inbox') {

                    if(selectedMatter != "" ) {

                        $('#inbox-subtab #email-list').find('.email-card').each(function() {

                            if ($(this).data('matterid') == selectedMatter) {

                                $(this).show();

                            } else {

                                $(this).hide();

                            }

                        });

                    }  else {

                        $(this).hide();

                    }

                }



                else if( activeTab == 'conversations' && activeSubTab == 'sent') {

                    if(selectedMatter != "" ) {

                        $('#sent-subtab #email-list1').find('.email-card').each(function() {

                            if ($(this).data('matterid') == selectedMatter) {

                                $(this).show();

                            } else {

                                $(this).hide();

                            }

                        });

                    }  else {

                        $(this).hide();

                    }

                }

            } else {

                $('#sel_matter_id_client_detail').prop('disabled', false).trigger('change');

                $('#sel_matter_id_client_detail').attr('data-valid', 'required').trigger('change');

                selectedMatter = "";

            }

        });



        //Select matter drop down chnage

        $('#sel_matter_id_client_detail').on('change', function() {

            selectedMatter = $(this).val();

            var uniqueMatterNo = $(this).find('option:selected').data('clientuniquematterno');

            var currentUrl = window.location.href;

            // Get the active tab

            var activeTab = $('.tab-button.active, .vertical-tab-button.active, .client-nav-button.active').data('tab') || 'personaldetails';



             // Get the active sub tab

            var activeSubTab = $('.subtab-button.active').data('subtab');

            

        // Skip redirect during initialization

        if (isInitializing) {

            return;

        }



        // Prevent redirect when "Select Matters" placeholder is selected

        if (selectedMatter === '' || selectedMatter === null) {

            console.log('Select Matters placeholder selected - no redirect');

            return;

        }



        // Split the URL into segments

        var urlSegments = currentUrl.split('/');

        var baseUrl;

        var clientId = window.ClientDetailConfig.encodeId;

        

        // Build new URL with matter and tab

        baseUrl = '/clients/detail/' + clientId;



        var newUrl;

        if (selectedMatter != '' && uniqueMatterNo) {

            // Append the new matter ID and active tab to the base URL

            newUrl = baseUrl + '/' + uniqueMatterNo + '/' + activeTab;

        } else {

            // If no matter is selected, redirect to the base URL with just the tab

            newUrl = baseUrl + '/' + activeTab;

        }

        

        // Only redirect if the URL is actually changing to prevent infinite loops

        if (currentUrl.split('?')[0] !== newUrl && !currentUrl.endsWith(newUrl)) {

            window.location.href = newUrl;

            return; // Exit early to prevent further execution

        }



            if( activeTab == 'noteterm' ) {

                // Apply combined matter and task group filtering
                const activeTaskGroup = $('.subtab8-button.active').data('subtab8') || 'All';
                
                $('#noteterm-tab').find('.note-card-redesign').each(function() {
                    const noteMatterId = $(this).data('matterid');
                    const noteType = $(this).data('type');
                    
                    let showNote = false;
                    
                    // Matter filtering logic
                    if (selectedMatter !== "") {
                        // Show notes that match the selected matter OR notes with no matter_id
                        showNote = (noteMatterId == selectedMatter || noteMatterId == '' || noteMatterId == null);
                    } else {
                        // Show all notes when no matter is selected
                        showNote = true;
                    }
                    
                    // Task group filtering logic
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

            else if( activeTab == 'documentalls' && activeSubTab == 'migrationdocuments') {

                if(selectedMatter != "" ) {

                    $('#migrationdocuments-subtab .migdocumnetlist1').find('.drow').each(function() {

                        if ($(this).data('matterid') == selectedMatter) {

                            $(this).show();

                        } else {

                            $(this).hide();

                        }

                    });

                }  else {

                    $(this).hide();

                }

            }



            else if( activeTab == 'conversations' && activeSubTab == 'inbox') {

                if(selectedMatter != "" ) {

                    $('#inbox-subtab #email-list').find('.email-card').each(function() {

                        if ($(this).data('matterid') == selectedMatter) {

                            $(this).show();

                        } else {

                            $(this).hide();

                        }

                    });

                }  else {

                    $(this).hide();

                }

            }



             else if( activeTab == 'conversations' && activeSubTab == 'sent') {

                if(selectedMatter != "" ) {

                    $('#sent-subtab #email-list1').find('.email-card').each(function() {

                        if ($(this).data('matterid') == selectedMatter) {

                            $(this).show();

                        } else {

                            $(this).hide();

                        }

                    });

                }  else {

                    $(this).hide();

                }

            }



            //var activeTab = $('.nav-item .nav-link.active');

            /*if( activeTab.attr('id') == 'noteterm-tab' ) {

                // Trigger click on the active tab

                activeTab.trigger('click');

            }

            else if( activeTab.attr('id') == 'migrationdocuments-tab' ) {

                // Trigger click on the active tab

                activeTab.trigger('click');

            }

            else if( activeTab.attr('id') == 'conversations-tab' ) {

                // Trigger click on the active tab

                activeTab.trigger('click');

            }*/

        });





        //Tab click

        $(document).delegate('#client_tabs a', 'click', function(){

            // Get the target tab's href

            var target = $(this).attr('href');



            // Reset the visibility and classes

            $('.left_section').hide(); // Hide the left section by default

            $('.right_section').parent().removeClass('col-8 col-md-8 col-lg-8').addClass('col-12 col-md-12 col-lg-12');



            // Adjust based on the selected tab

            if (target === '#activities') {

                $('.left_section').show(); // Show the left section for Activities tab

                $('.left_section').removeClass('col-4 col-md-4 col-lg-4').addClass('col-4 col-md-4 col-lg-4');

                $('.right_section').parent().removeClass('col-12 col-md-12 col-lg-12').addClass('col-8 col-md-8 col-lg-8');

            }



            else if (target === '#noteterm') {

                if ($('.general_matter_checkbox_client_detail').is(':checked')) {

                    selectedMatter = $('.general_matter_checkbox_client_detail').val();

                } else {

                    selectedMatter = $('#sel_matter_id_client_detail').val();

                }

                

                if(target == '#noteterm' ){

                    if(selectedMatter != "" ) {

                        $(target).find('.note_col').each(function() {

                            if ($(this).data('matterid') == selectedMatter) {

                                $(this).show();

                            } else {

                                $(this).hide();

                            }

                        });

                    }  else {

                        //alert('Please select matter from matter drop down.');

                        $(target).find('.note_col').each(function() {

                            $(this).hide();

                        });

                    }

                }

            }



            else if (target === '#migrationdocuments') { //alert('migrationdocuments');

                if ($('.general_matter_checkbox_client_detail').is(':checked')) {

                    selectedMatter = $('.general_matter_checkbox_client_detail').val();

                } else {

                    selectedMatter = $('#sel_matter_id_client_detail').val();

                }

                if(target == '#migrationdocuments' ){

                    if(selectedMatter != "" ) {

                        $(target).find('.drow').each(function() {

                            if ($(this).data('matterid') == selectedMatter) {

                                $(this).show();

                            } else {

                                $(this).hide();

                            }

                        });

                    }  else {

                        //alert('Please select matter from matter drop down.');

                        $(target).find('.drow').each(function() {

                            $(this).hide();

                        });

                    }

                }

            }



            else if (target === '#conversations') {

                if ($('.general_matter_checkbox_client_detail').is(':checked')) {

                    selectedMatter = $('.general_matter_checkbox_client_detail').val();

                } else {

                    selectedMatter = $('#sel_matter_id_client_detail').val();

                }

                if(target == '#conversations' ){

                    if(selectedMatter != "" ) {

                        //inbox mail

                        $(target).find('.inbox_conversation_list').each(function() {

                            if ($(this).data('matterid') == selectedMatter) {

                                $(this).show();

                            } else {

                                $(this).hide();

                            }

                        });



                        //sent mail

                        $(target).find('.sent_conversation_list').each(function() {

                            if ($(this).data('matterid') == selectedMatter) {

                                $(this).show();

                            } else {

                                $(this).hide();

                            }

                        });

                    }  else {

                        //alert('Please select matter from matter drop down.');

                        //inbox mail

                        $(target).find('.inbox_conversation_list').each(function() {

                            $(this).hide();

                        });



                        //sent mail

                        $(target).find('.sent_conversation_list').each(function() {

                            $(this).hide();

                        });

                    }

                }

            }

            else if (target === '#clientdetailform') {

                var right_section_height = $('#clientdetailform').height();

                right_section_height = right_section_height+200;

                $('.right_section').css({"maxHeight":right_section_height});

            }

        });



        $(document).delegate('.general_matter_checkbox_client_detail', 'click', function(){

            // Uncheck all checkboxes

            $('.general_matter_checkbox_client_detail').not(this).prop('checked', false);

        });

        //Matter checkbox end





        //create client receipt start

        $('.report_date_fields').datepicker({ format: 'dd/mm/yyyy',autoclose: true });

        $('.report_entry_date_fields').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());



        /*$(document).delegate('.openproductrinfo', 'click', function(){

            var clonedval = $('.clonedrow').html();

            $('.productitem').append('<tr class="product_field_clone">'+clonedval+'</tr>');

            $('.report_date_fields,.report_entry_date_fields').datepicker({ format: 'dd/mm/yyyy', autoclose: true  });

           // $('.report_entry_date_fields').last().datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

        });*/



        $(document).delegate('.openproductrinfo', 'click', function() {

            var clonedval = $('.clonedrow').html();

            var $newRow = $('<tr class="product_field_clone">' + clonedval + '</tr>');

            // Hide the invoice input inside the cloned row

            $newRow.find('.invoice_no_cls').hide();

            $('.productitem').append($newRow);

            $('.report_date_fields,.report_entry_date_fields').datepicker({ format: 'dd/mm/yyyy', autoclose: true  });

            //$('.report_entry_date_fields').last().datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

        });





        $(document).delegate('.removeitems', 'click', function(){

            var $tr    = $(this).closest('.product_field_clone');

            var trclone = $('.product_field_clone').length;

            if(trclone > 0){

                $tr.remove();

            }

            grandtotalAccountTab();

        });



        $(document).delegate('.deposit_amount_per_row,.withdraw_amount_per_row', 'keyup', function(){

            grandtotalAccountTab();

        });



        $.ajaxSetup({

            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            }

        });







        function getInfoByReceiptId11(receiptid) {

            $.ajax({

                type:'post',

                url: window.ClientDetailConfig.urls.getInfoByReceiptId,

                sync:true,

                data: {receiptid:receiptid},

                success: function(response){

                    var obj = $.parseJSON(response);

                    if(obj.status){

                        $('#function_type').val("edit");

                        $('#createreceiptmodal').modal('show');



                        const invoiceRadio = document.querySelector('input[name="receipt_type"][value="invoice_receipt"]');

                        if (invoiceRadio) {

                            invoiceRadio.checked = true;



                            // Manually trigger the change event

                            invoiceRadio.dispatchEvent(new Event('change'));

                        }



                        if(obj.record_get){

                            var record_get = obj.record_get;

                            //var trRows_office = "";

                            var sum = 0;

                            $('.productitem_invoice tr.clonedrow_invoice').remove();

                            $('.productitem_invoice tr.product_field_clone_invoice').remove();

                            $.each(record_get, function(index, subArray) {

                                var value_sum = parseFloat(subArray.withdraw_amount);

                                if (!isNaN(value_sum)) {

                                    sum += value_sum;

                                }



                                var rowCls = index < 1 ? 'clonedrow_invoice' : 'product_field_clone_invoice';



                                //var trRows_office = '<tr class="'+rowCls+'"><td><input name="id[]" type="hidden" value="'+subArray.id+'" /><input data-valid="required" class="form-control report_date_fields_invoice" name="trans_date[]" type="text" value="'+subArray.trans_date+'" /></td><td><input data-valid="required" class="form-control report_date_fields_invoice" name="entry_date[]" type="text" value="'+subArray.entry_date+'" /></td><td><select class="form-control gst_included_cls" name="gst_included[]"><option value="">Select</option><option value="Yes">Yes</option><option value="No">No</option></select></td><td><select class="form-control payment_type_cls" name="payment_type[]"><option value="">Select</option><option value="Professional Fee">Professional Fee</option><option value="Department Charges">Department Charges</option><option value="Surcharge">Surcharge</option><option value="Disbursements">Disbursements</option><option value="Other Cost">Other Cost</option></select></td><td><input data-valid="required" class="form-control" name="description[]" type="text" value="'+subArray.description+'" /></td><td><span class="currencyinput">$</span><input data-valid="required" class="form-control withdraw_amount_invoice_per_row" name="withdraw_amount[]" type="text" value="'+subArray.withdraw_amount+'" /></td><td><a class="removeitems_invoice" href="javascript:;"><i class="fa fa-times"></i></a></td></tr>';

                                var trRows_office = `<tr class="${rowCls}">

                                    <td>

                                        <input name="id[]" type="hidden" value="${subArray.id}" />

                                        <input data-valid="required" class="form-control report_date_fields_invoice" name="trans_date[]" type="text" value="${subArray.trans_date}" />

                                    </td>

                                    <td>

                                        <input data-valid="required" class="form-control report_date_fields_invoice" name="entry_date[]" type="text" value="${subArray.entry_date}" />

                                    </td>



                                    <td>

                                        <select class="form-control gst_included_cls" name="gst_included[]">

                                            <option value="">Select</option>

                                            <option value="Yes">Yes</option>

                                            <option value="No">No</option>

                                        </select>

                                    </td>

                                    <td>

                                        <select class="form-control payment_type_cls" name="payment_type[]">

                                            <option value="">Select</option>

                                            <option value="Professional Fee">Professional Fee</option>

                                            <option value="Department Charges">Department Charges</option>

                                            <option value="Surcharge">Surcharge</option>

                                            <option value="Disbursements">Disbursements</option>

                                            <option value="Other Cost">Other Cost</option>

                                            <option value="Discount">Discount</option>

                                        </select>

                                    </td>

                                    <td>

                                        <input data-valid="required" class="form-control" name="description[]" type="text" value="${subArray.description}" />

                                    </td>

                                    <td>

                                        <span class="currencyinput" style="display: inline-block;color: #34395e;">$</span>

                                        <input data-valid="required" style="display: inline-block;" class="form-control withdraw_amount_invoice_per_row" name="withdraw_amount[]" type="text" value="${subArray.withdraw_amount}" />

                                    </td>

                                    <td>

                                        <a class="removeitems_invoice" href="javascript:;"><i class="fa fa-times"></i></a>

                                    </td>

                                </tr>`;



                                let $newRow = $(trRows_office);

                                $('.productitem_invoice').append($newRow);



                                // Set selected values

                                $newRow.find('.gst_included_cls').val(subArray.gst_included);

                                $newRow.find('.payment_type_cls').val(subArray.payment_type);



                                $('.report_date_fields_invoice').datepicker({ format: 'dd/mm/yyyy', autoclose: true  });

                                $('.report_entry_date_fields_invoice').last().datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

                                if(index <1 ){

                                    $('.invoice_no').val(subArray.invoice_no);

                                    $('.unique_invoice_no').text(subArray.invoice_no);

                                    $('#receipt_id').val(subArray.receipt_id);

                                }

                            });

                            $('.total_withdraw_amount_all_rows_invoice').text("$"+sum.toFixed(2));

                        }

                    }

                }

            });

        }



        function getInfoByReceiptId(receiptid) {

            $.ajax({

                type: 'post',

                url: window.ClientDetailConfig.urls.getInfoByReceiptId,

                sync: true,

                data: { receiptid: receiptid },

                success: function (response) {

                    var obj = $.parseJSON(response);

                    if (obj.status) {

                        $('#function_type').val("edit");

                        $('#createreceiptmodal').modal('show');



                        const invoiceRadio = document.querySelector('input[name="receipt_type"][value="invoice_receipt"]');

                        if (invoiceRadio) {

                            invoiceRadio.checked = true;

                            invoiceRadio.dispatchEvent(new Event('change'));

                        }



                        if (obj.record_get) {

                            var record_get = obj.record_get;

                            var sum = 0;

                            $('.productitem_invoice tr.clonedrow_invoice').remove();

                            $('.productitem_invoice tr.product_field_clone_invoice').remove();



                            $.each(record_get, function (index, subArray) {

                                var value_sum = parseFloat(subArray.withdraw_amount);

                                if (!isNaN(value_sum)) {

                                    sum += value_sum;

                                }



                                var rowCls = index < 1 ? 'clonedrow_invoice' : 'product_field_clone_invoice';



                                var trRows_office = `<tr class="${rowCls}">

                                    <td>

                                        <input name="id[]" type="hidden" value="${subArray.id}" />

                                        <input data-valid="required" class="form-control report_date_fields_invoice" name="trans_date[]" type="text" value="${subArray.trans_date}" />

                                    </td>

                                    <td>

                                        <input data-valid="required" class="form-control report_date_fields_invoice" name="entry_date[]" type="text" value="${subArray.entry_date}" />

                                    </td>

                                    <td>

                                        <select class="form-control gst_included_cls" name="gst_included[]">

                                            <option value="">Select</option>

                                            <option value="Yes" ${subArray.gst_included === 'Yes' ? 'selected' : ''}>Yes</option>

                                            <option value="No" ${subArray.gst_included === 'No' ? 'selected' : ''}>No</option>

                                        </select>

                                    </td>

                                    <td>

                                        <select class="form-control payment_type_cls" name="payment_type[]">

                                            <option value="">Select</option>

                                            <option value="Professional Fee" ${subArray.payment_type === 'Professional Fee' ? 'selected' : ''}>Professional Fee</option>

                                            <option value="Department Charges" ${subArray.payment_type === 'Department Charges' ? 'selected' : ''}>Department Charges</option>

                                            <option value="Surcharge" ${subArray.payment_type === 'Surcharge' ? 'selected' : ''}>Surcharge</option>

                                            <option value="Disbursements" ${subArray.payment_type === 'Disbursements' ? 'selected' : ''}>Disbursements</option>

                                            <option value="Other Cost" ${subArray.payment_type === 'Other Cost' ? 'selected' : ''}>Other Cost</option>

                                            <option value="Discount" ${subArray.payment_type === 'Discount' ? 'selected' : ''}>Discount</option>

                                        </select>

                                    </td>

                                    <td>

                                        <input data-valid="required" class="form-control" name="description[]" type="text" value="${subArray.description}" />

                                    </td>

                                    <td>

                                        <span class="currencyinput" style="display: inline-block;color: #34395e;">$</span>

                                        <input data-valid="required" style="display: inline-block;" class="form-control withdraw_amount_invoice_per_row" name="withdraw_amount[]" type="text" value="${subArray.withdraw_amount}" />

                                    </td>

                                    <td>

                                        <a class="removeitems_invoice" href="javascript:;"><i class="fa fa-times"></i></a>

                                    </td>

                                </tr>`;



                                let $newRow = $(trRows_office);

                                $('.productitem_invoice').append($newRow);



                                $('.report_date_fields_invoice').datepicker({ format: 'dd/mm/yyyy', autoclose: true });

                                $('.report_entry_date_fields_invoice').last().datepicker({ format: 'dd/mm/yyyy', todayHighlight: true, autoclose: true }).datepicker('setDate', new Date());



                                if (index < 1) {

                                    $('.invoice_no').val(subArray.invoice_no);

                                    $('.unique_invoice_no').text(subArray.invoice_no);

                                    $('#receipt_id').val(subArray.receipt_id);

                                }

                            });



                            $('.total_withdraw_amount_all_rows_invoice').text("$" + sum.toFixed(2));

                        }

                    }

                }

            });

        }





        $(document).on('change', '.client_fund_ledger_type', function () {

            var $row = $(this).closest('tr');

            var ledgerType = $(this).val();



            var $depositInput = $row.find('.deposit_amount_per_row');

            var $withdrawInput = $row.find('.withdraw_amount_per_row');

            var $invoiceInput = $row.find('.invoice_no_cls');



            // Invoice show/hide based on Fee Transfer

            if (ledgerType === 'Fee Transfer') {

                $invoiceInput.show().attr('data-valid', 'required');

            } else {

                $invoiceInput.hide().removeAttr('data-valid').val('');

            }



            if (ledgerType !== "") {

                var fundType = (ledgerType === 'Deposit') ? 'deposit' : 'withdraw';



                if (fundType === 'deposit') {

                    $depositInput.removeAttr('readonly').attr('data-valid', 'required').val("");

                    $withdrawInput.attr('readonly', 'readonly').removeAttr('data-valid').val("");

                } else if (fundType === 'withdraw') {

                    $withdrawInput.removeAttr('readonly').attr('data-valid', 'required').val("");

                    $depositInput.attr('readonly', 'readonly').removeAttr('data-valid').val("");

                } else {

                    $depositInput.attr('readonly', 'readonly').removeAttr('data-valid').val("");

                    $withdrawInput.attr('readonly', 'readonly').removeAttr('data-valid').val("");

                }

            } else {

                $depositInput.attr('readonly', 'readonly').removeAttr('data-valid').val("");

                $withdrawInput.attr('readonly', 'readonly').removeAttr('data-valid').val("");

            }

        });





        function grandtotalAccountTab() {

            var total_deposit_amount_all_rows = 0;

            var total_withdraw_amount_all_rows = 0;



            $('.productitem tr').each(function() {

                var $row = $(this);



                // Handle deposit amount

                var depositVal = $row.find('.deposit_amount_per_row').val();

                var depositAmount = parseFloat(depositVal) || 0; // fallback to 0 if NaN

                total_deposit_amount_all_rows += depositAmount;



                // Handle withdraw amount

                var withdrawVal = $row.find('.withdraw_amount_per_row').val();

                var withdrawAmount = parseFloat(withdrawVal) || 0; // fallback to 0 if NaN

                total_withdraw_amount_all_rows += withdrawAmount;

            });



            $('.total_deposit_amount_all_rows').html("$" + total_deposit_amount_all_rows.toFixed(2));

            $('.total_withdraw_amount_all_rows').html("$" + total_withdraw_amount_all_rows.toFixed(2));

        }



        //create client receipt changes end





        //create invoice receipt start

        $('.report_date_fields_invoice').datepicker({ format: 'dd/mm/yyyy', autoclose: true });

        $('.report_entry_date_fields_invoice').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());





        $(document).delegate('.openproductrinfo_invoice', 'click', function(){

            var clonedval_invoice = `<td>

                            <input name="id[]" type="hidden" value="" />

                            <input data-valid="required" class="form-control report_date_fields_invoice" name="trans_date[]" type="text" value="" />

                        </td>

                        <td>

                            <input data-valid="required" class="form-control report_entry_date_fields_invoice" name="entry_date[]" type="text" value="" />

                        </td>



                        <td>

                            <select class="form-control" name="gst_included[]">

                                <option value="">Select</option>

                                <option value="Yes">Yes</option>

                                <option value="No">No</option>

                            </select>

                        </td>



                        <td>

                            <select class="form-control payment_type_invoice_per_row" name="payment_type[]">

                                <option value="">Select</option>

                                <option value="Professional Fee">Professional Fee</option>

                                <option value="Department Charges">Department Charges</option>

                                <option value="Surcharge">Surcharge</option>

                                <option value="Disbursements">Disbursements</option>

                                <option value="Other Cost">Other Cost</option>

                                <option value="Discount">Discount</option>



                            </select>

                        </td>

                        <td>

                            <input data-valid="required" class="form-control" name="description[]" type="text" value="" />

                        </td>



                        <td>

                            <span class="currencyinput" style="display: inline-block;color: #34395e;">$</span>

                            <input data-valid="required" style="display: inline-block;" class="form-control withdraw_amount_invoice_per_row" name="withdraw_amount[]" type="text" value="" />

                        </td>



                        <td>

                            <a class="removeitems_invoice" href="javascript:;"><i class="fa fa-times"></i></a>

                        </td>>`;



                //var clonedval_invoice = $('.clonedrow_invoice').html();

                $('.productitem_invoice').append('<tr class="product_field_clone_invoice">'+clonedval_invoice+'</tr>');

                $('.report_date_fields_invoice,.report_entry_date_fields_invoice').datepicker({ format: 'dd/mm/yyyy', autoclose: true });

                $('.report_entry_date_fields_invoice').last().datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

        });



        $(document).delegate('.removeitems_invoice', 'click', function(){

            var $tr_invoice    = $(this).closest('.product_field_clone_invoice');

            var trclone_invoice = $('.product_field_clone_invoice').length;

            if(trclone_invoice > 0){

                $tr_invoice.remove();

            }

            grandtotalAccountTab_invoice();

        });



        $(document).delegate('.withdraw_amount_invoice_per_row, .payment_type_invoice_per_row', 'blur', function() {

            grandtotalAccountTab_invoice();

        });



      



        function grandtotalAccountTab_invoice() {

            var total_withdraw_amount_all_rows_invoice = 0;



            // Loop through only visible rows

            $('.productitem_invoice tr:visible').each(function(index) {

                var $row = $(this);



                // Get the withdraw amount from the input field

                var withdrawVal = $row.find('.withdraw_amount_invoice_per_row').val();

                // Get the payment type from the select field

                var paymentType = $row.find('select[name="payment_type[]"]').val();



                if (withdrawVal) {

                    // Remove currency symbols, commas, and spaces

                    withdrawVal = withdrawVal.replace(/[^0-9.-]+/g, '');

                    var withdrawAmount = parseFloat(withdrawVal) || 0; // Fallback to 0 if NaN



                    // Adjust total based on payment type

                    if (paymentType === 'Discount') {

                        total_withdraw_amount_all_rows_invoice -= withdrawAmount;

                    } else {

                        total_withdraw_amount_all_rows_invoice += withdrawAmount;

                    }



                    console.log('Row ' + (index + 1) + ': withdrawAmount = ' + withdrawAmount + ', Payment Type = ' + paymentType);

                } else {

                    console.log('Row ' + (index + 1) + ': No withdraw amount found');

                }

            });



            //console.log('Total calculated: ' + total_withdraw_amount_all_rows_invoice);

            $('.total_withdraw_amount_all_rows_invoice').html('$' + total_withdraw_amount_all_rows_invoice.toFixed(2));

        }





        //create invoice changes end





        //create office receipt start

        $('.report_date_fields_office').datepicker({ format: 'dd/mm/yyyy', autoclose: true });

        $('.report_entry_date_fields_office').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());



        $(document).delegate('.openproductrinfo_office', 'click', function(){

            var clonedval_office = $('.clonedrow_office').html();

            $('.productitem_office').append('<tr class="product_field_clone_office">'+clonedval_office+'</tr>');

            $('.report_date_fields_office,.report_entry_date_fields_office').datepicker({ format: 'dd/mm/yyyy', autoclose: true });

            $('.report_entry_date_fields_office').last().datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

        });



        $(document).delegate('.removeitems_office', 'click', function(){

            var $tr_office    = $(this).closest('.product_field_clone_office');

            var trclone_office = $('.product_field_clone_office').length;

            if(trclone_office > 0){

                $tr_office.remove();

            }

            grandtotalAccountTab_office();

        });



        $(document).delegate('.total_deposit_amount_office', 'keyup', function(){

            grandtotalAccountTab_office();

        });



        function grandtotalAccountTab_office() {

            var total_deposit_amount_all_rows = 0;

            $('.productitem_office tr').each(function() {

                var $row = $(this);



                // Handle deposit amount

                var depositVal = $row.find('.total_deposit_amount_office').val();

                var depositAmount = parseFloat(depositVal) || 0; // fallback to 0 if NaN

                total_deposit_amount_all_rows += depositAmount;

            });



            $('.total_deposit_amount_all_rows_office').html("$" + total_deposit_amount_all_rows.toFixed(2));

        }

        //create office receipt changes end





        //create journal receipt start

        $('.report_date_fields_journal').datepicker({ format: 'dd/mm/yyyy', autoclose: true });

        $('.report_entry_date_fields_journal').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());



        $(document).delegate('.openproductrinfo_journal', 'click', function(){

            var clonedval_journal = $('.clonedrow_journal').html();

            $('.productitem_journal').append('<tr class="product_field_clone_journal">'+clonedval_journal+'</tr>');

            $('.report_date_fields_journal').datepicker({ format: 'dd/mm/yyyy', autoclose: true });

            $('.report_entry_date_fields_journal').last().datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

        });



        $(document).delegate('.removeitems_journal', 'click', function(){

            var $tr_journal    = $(this).closest('.product_field_clone_journal');

            var trclone_journal = $('.product_field_clone_journal').length;

            if(trclone_journal > 0){

                $tr_journal.remove();

            }

            grandtotalAccountTab_journal();

        });



        $(document).delegate('.total_withdrawal_amount_journal,.total_deposit_amount_journal', 'keyup', function(){

            grandtotalAccountTab_journal();

        });



        $(document).delegate('.total_withdrawal_amount_journal', 'blur', function(){

            if( $(this).val() != ""){

                var randomNumber = $('#journal_top_value_db').val();

                randomNumber = Number(randomNumber);

                randomNumber = randomNumber + 1; 

                $('#journal_top_value_db').val(randomNumber);

                randomNumber = "Trans"+randomNumber;

                $(this).closest('tr').find('.unique_trans_no_journal').val(randomNumber);

                $(this).closest('tr').find('.unique_trans_no_hidden_journal').val(randomNumber);

            } else {

                $(this).closest('tr').find('.unique_trans_no_journal').val();

                $(this).closest('tr').find('.unique_trans_no_hidden_journal').val();

            }

        });



        $(document).delegate('.total_deposit_amount_journal', 'blur', function(){

            if( $(this).val() != ""){

                var randomNumber = $('#journal_top_value_db').val();

                randomNumber = Number(randomNumber);

                randomNumber = randomNumber + 1; 

                $('#journal_top_value_db').val(randomNumber);

                randomNumber = "Rec"+randomNumber;

                $(this).closest('tr').find('.unique_trans_no_journal').val(randomNumber);

                $(this).closest('tr').find('.unique_trans_no_hidden_journal').val(randomNumber);

            } else {

                $(this).closest('tr').find('.unique_trans_no_journal').val();

                $(this).closest('tr').find('.unique_trans_no_hidden_journal').val();

            }

        });



        function grandtotalAccountTab_journal(){

            var total_withdrawal_amount_all_rows_journal = 0;

            $('.productitem_journal tr').each(function(){

            if($(this).find('.total_withdrawal_amount_journal').val() != ''){

                    var withdrawal_amount_per_row_journal = $(this).find('.total_withdrawal_amount_journal').val();

                }else{

                    var withdrawal_amount_per_row_journal = 0;

                }

                total_withdrawal_amount_all_rows_journal += parseFloat(withdrawal_amount_per_row_journal);

            });

            $('.total_withdraw_amount_all_rows_journal').html("$"+total_withdrawal_amount_all_rows_journal.toFixed(2));

        }

        //create journal receipt changes end



        $('#edu_service_start_date').datepicker({

            format: 'dd/mm/yyyy',

            autoclose: true

        });



        $('.filter_btn').on('click', function(){

            $('.filter_panel').slideToggle();

        });



        //Service type on chnage div

        $('.modal-body form#createservicetaken input[name="service_type"]').on('change', function(){

            var invid = $(this).attr('id');

            if(invid == 'Migration_inv'){

                $('.modal-body form#createservicetaken .is_Migration_inv').show();

                $('.modal-body form#createservicetaken .is_Migration_inv input').attr('data-valid', 'required');

                $('.modal-body form#createservicetaken .is_Eductaion_inv').hide();

                $('.modal-body form#createservicetaken .is_Eductaion_inv input').attr('data-valid', '');

            }

            else {

                $('.modal-body form#createservicetaken .is_Eductaion_inv').show();

                $('.modal-body form#createservicetaken .is_Eductaion_inv input').attr('data-valid', 'required');

                $('.modal-body form#createservicetaken .is_Migration_inv').hide();

                $('.modal-body form#createservicetaken .is_Migration_inv input').attr('data-valid', '');

            }

        });



        //Set select2 drop down box width

        $('#changeassignee').select2();

        $('#changeassignee').next('.select2-container').first().css('width', '220px');



        var windowsize = $(window).width();

        if(windowsize > 2000){

            $('.add_note').css('width','980px');

        }



        /////////////////////////////////////////////

        ////// not picked call button code start /////////

        /////////////////////////////////////////////

        $(document).delegate('.not_picked_call', 'click', function (e) {

            var clientName = window.ClientDetailConfig.clientFirstName || 'user';

            clientName = clientName.charAt(0).toUpperCase() + clientName.slice(1).toLowerCase(); //alert(clientName);



            var message = `Hi ${clientName},

We tried reaching you but couldn't connect. Please call us at 0396021330 or let us know a suitable time.

Please do not reply via SMS.

Bansal Immigration`;

        

            $('#messageText').val(message); // Set dynamic message text

            $('#notPickedCallModal').modal('show'); // Show Modal Window



            $('.sendMessage').on('click', function () {

                var message = $('#messageText').val();

                var not_picked_call = 1;

                $.ajax({

                    url: window.ClientDetailConfig.urls.notPickedCall,

                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

                    type: 'POST',

                    datatype: 'json',

                    data: {

                        id: window.ClientDetailConfig.clientId,

                        not_picked_call: not_picked_call,

                        message: message

                    },

                    success: function (response) {

                        var obj = $.parseJSON(response);

                        if (obj.not_picked_call == 1) {

                            alert(obj.message);

                        } else {

                            alert(obj.message);

                        }

                        getallactivities();

                        $('#notPickedCallModal').modal('hide'); // Hide Modal Window

                    }

                });

            });

        });



        /////////////////////////////////////////////

        ////// not picked call button code end //////

        /////////////////////////////////////////////



        /////////////////////////////////////////////

        ////// appointment popup chnages start //////

        /////////////////////////////////////////////



        $(document).delegate('.enquiry_item', 'change', function(){

            var id = $(this).val();

            if(id != ""){

                var v = 'services';

                if(id == 8){  //If nature of service == INDIA/UK/CANADA/EUROPE TO AUSTRALIA

                    $('#serviceval_2').hide();

                } else {

                    $('#serviceval_2').show();

                }



                $('.services_row').show();

                $('#myTab .nav-item #nature_of_enquiry-tab').addClass('disabled');

                $('#myTab .nav-item #services-tab').removeClass('disabled');

                $('#myTab a[href="#'+v+'"]').trigger('click');



                $('.services_item').prop('checked', false);

                $('.appointment_row').hide();

                $('.info_row').hide();

                $('.confirm_row').hide();



                $('.timeslots').html('');

                $('.showselecteddate').html('');



                $('#timeslot_col_date').val("");

                $('#timeslot_col_time').val(""); //Do blank Timeslot selected date and time

            } else {

                var v = 'nature_of_enquiry';

                $('.services_row').hide();

                $('.appointment_row').hide();

                $('.info_row').hide();

                $('.confirm_row').hide();



                $('#myTab .nav-item #services-tab').addClass('disabled');

                $('#myTab .nav-item #nature_of_enquiry-tab').removeClass('disabled');

                $('#myTab a[href="#'+v+'"]').trigger('click');

            }

            $('input[name="noe_id"]').val(id);

        });



        $(document).on('change', '.inperson_address', function() {

            var id = $("input[name='inperson_address']:checked").attr('data-val'); //alert(id);

            if(id != ""){

                var v = 'info';

                $('.info_row').show();

                $('.appointment_details_cls').show();



                $('#myTab .nav-item #appointment_details-tab').addClass('disabled');

                $('#myTab .nav-item #info-tab').removeClass('disabled');

                $('#myTab a[href="#'+v+'"]').trigger('click');

            } else {

                var v = 'appointment_details';

                $('.info_row').hide();

                $('.appointment_details_cls').hide();

                $('.confirm_row').hide();



                $('#myTab .nav-item #info-tab').addClass('disabled');

                $('#myTab .nav-item #appointment_details-tab').removeClass('disabled');

                $('#myTab a[href="#'+v+'"]').trigger('click');

            }

            //console.log($("input[name='radioGroup']:checked").val());



            $("input[name='inperson_address']:checked").val(id);

            $('.timeslots').html('');

            if(id != ""){

                var enquiry_item  = $('.enquiry_item').val(); //alert(enquiry_item);

                var service_id = $("input[name='radioGroup']:checked").val(); //alert(service_id);

                var inperson_address = $("input[name='inperson_address']:checked").attr('data-val'); //alert(inperson_address);

                $.ajax({

                    url:window.ClientDetailConfig.urls.getDateTimeBackend,

                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

                    type:'POST',

                    data:{id:service_id, enquiry_item:enquiry_item, inperson_address:inperson_address },

                    datatype:'json',

                    success:function(res){

                        var obj = JSON.parse(res);

                        if(obj.success){

                            duration = obj.duration;

                            daysOfWeek =  obj.weeks;

                            starttime =  obj.start_time;

                            endtime =  obj.end_time;

                            disabledtimeslotes = obj.disabledtimeslotes;

                            var datesForDisable = obj.disabledatesarray;



                            $('#datetimepicker').datepicker({

                                inline: true,

                                startDate: new Date(),

                                datesDisabled: datesForDisable,

                                daysOfWeekDisabled: daysOfWeek,

                                format: 'dd/mm/yyyy'

                            }).on('changeDate', function(e) {

                                var date = e.format();

                                var checked_date=e.date.toLocaleDateString('en-US');



                                $('.showselecteddate').html(date);

                                $('input[name="date"]').val(date);

                                $('#timeslot_col_date').val(date);





                                $('.timeslots').html('');

                                var start_time = parseTime(starttime),

                                end_time = parseTime(endtime),

                                interval = parseInt(duration);

                                var service_id = $("input[name='radioGroup']:checked").val(); //alert(service_id);

                                var inperson_address = $("input[name='inperson_address']:checked").attr('data-val'); //alert(inperson_address);

                                var enquiry_item  = $('.enquiry_item').val(); //alert(enquiry_item);

                                //var slot_overwrite_hidden = $('#slot_overwrite_hidden').val();

                                $.ajax({

                                    url:window.ClientDetailConfig.urls.getDisabledDateTime,

                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

                                    type:'POST',

                                    data:{service_id:service_id,sel_date:date, enquiry_item:enquiry_item,inperson_address:inperson_address},

                                    datatype:'json',

                                    success:function(res){

                                        $('.timeslots').html('');

                                        var obj = JSON.parse(res);

                                        if(obj.success){

                                            if( $('#slot_overwrite_hidden').val() == 1){

                                                var objdisable = [];

                                            } else {

                                                var objdisable = obj.disabledtimeslotes;

                                            }

                                           

                                            var start_timer = start_time;

                                            for(var i = start_time; i<end_time; i = i+interval){

                                                var timeString = start_timer + interval;

                                                // Prepend any date. Use your birthday.

                                                const timeString12hr = new Date('1970-01-01T' + convertHours(start_timer) + 'Z')

                                                .toLocaleTimeString('en-US',

                                                    {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}

                                                );

                                                const timetoString12hr = new Date('1970-01-01T' + convertHours(timeString) + 'Z')

                                                .toLocaleTimeString('en-US',

                                                    {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}

                                                );



                                                var today_date = new Date();

                                                //const options = { timeZone: 'Australia/Sydney'};

                                                today_date = today_date.toLocaleDateString('en-US');



                                                // current time

                                                var now = new Date();

                                                var nowTime = new Date('1/1/1900 ' + now.toLocaleTimeString(navigator.language, {

                                                    hour: '2-digit',

                                                    minute: '2-digit',

                                                    hour12: true

                                                }));



                                                var current_time=nowTime.toLocaleTimeString('en-US');

                                                if(objdisable.length > 0){

                                                    if(jQuery.inArray(timeString12hr, objdisable) != -1  ) {



                                                    } else if ((checked_date == today_date) && (current_time > timeString12hr || current_time > timetoString12hr)){

                                                    } else{

                                                        $('.timeslots').append('<div data-fromtime="'+timeString12hr+'" data-totime="'+timetoString12hr+'" style="cursor: pointer;" class="timeslot_col"><span>'+timeString12hr+'</span></div>');

                                                    }

                                                } else{

                                                    if((checked_date == today_date) && (current_time > timeString12hr || current_time > timetoString12hr)){

                                                    } else {

                                                        $('.timeslots').append('<div data-fromtime="'+timeString12hr+'" data-totime="'+timetoString12hr+'" style="cursor: pointer;" class="timeslot_col"><span>'+timeString12hr+'</span></div>');

                                                    }

                                                }

                                                start_timer = timeString;

                                            }

                                        }else{



                                        }

                                    }

                                });

                            });



                            if(id != ""){

                                var v = 'appointment_details';

                                $('#myTab .nav-item #services-tab').addClass('disabled');

                                $('#myTab .nav-item #appointment_details-tab').removeClass('disabled');

                                $('#myTab a[href="#'+v+'"]').trigger('click');

                            } else {

                                var v = 'services';

                                $('#myTab .nav-item #services-tab').removeClass('disabled');

                                $('#myTab .nav-item #appointment_details-tab').addClass('disabled');

                                $('#myTab a[href="#'+v+'"]').trigger('click');

                            }

                            $('input[name="service_id"]').val($("input[name='radioGroup']:checked").val());

                        } else {

                            $('input[name="service_id"]').val('');

                            var v = 'services';

                            alert('There is a problem in our system. please try again');

                            $('#myTab .nav-item #services-tab').removeClass('disabled');

                            $('#myTab .nav-item #appointment_details-tab').addClass('disabled');

                        }

                    }

                })

            }

        });



        $(document).delegate('.appointment_item', 'change', function(){

            var id = $(this).val();

            if(id != ""){

                $('input[name="appointment_details"]').val(id);

            } else {

                $('input[name="appointment_details"]').val("");

            }

        });





        $(document).delegate('.services_item', 'change', function(){

            $('.info_row').hide();

            $('.confirm_row').hide();

            $("input[name='inperson_address']").prop("checked", false);

            $('.appointment_item').val("");

            $('.appointment_details_cls').hide();



            $('#timeslot_col_date').val("");

            $('#timeslot_col_time').val(""); //Do blank Timeslot selected date and time



            var id = $(this).val();

            if ($("input[name='radioGroup'][value='+id+']").prop("checked")) {

                $('#service_id').val(id);

            }

            //console.log($('#service_id').val());

            if( $('#service_id').val() == 1 ){ //paid

                $('.submitappointment_paid').show();

                $('.submitappointment').hide();

            } else { //free

                $('.submitappointment').show();

                $('.submitappointment_paid').hide();

            }



            if(id != ""){

                var v = 'appointment_details';

                if( id == 1 ){ //paid service

                    // Show the "Zoom / Google Meeting" option

                    $('select[name="appointment_details"] option[value="zoom_google_meeting"]').show();

                } else {

                    // Hide the "Zoom / Google Meeting" option

                    $('select[name="appointment_details"] option[value="zoom_google_meeting"]').hide();

                }

                $('.appointment_row').show();

            } else {

                var v = 'services';

                $('.appointment_row').hide();

            }

            $('.timeslots').html('');

            $('.showselecteddate').html('');

        });



        $('.slot_overwrite_time_dropdown').change(function() {

            $('#timeslot_col_time').val("");

            var currentSelVal = $(this).val();

            $('#timeslot_col_time').val(currentSelVal);

        });



        $('#slot_overwrite').change(function() {

            $('#timeslot_col_date').val("");

            $('#timeslot_col_time').val("");

            if ($(this).is(':checked')) { 

                $('#slot_overwrite_hidden').val(1);

                $('.timeslotDivCls').hide();

                $('.slotTimeOverwriteDivCls').show();

            } else { 

                $('#slot_overwrite_hidden').val(0);

                $('.timeslotDivCls').show();

                $('.slotTimeOverwriteDivCls').hide();

            }

        });





        $(document).delegate('.nextbtn', 'click', function(){

            var v = $(this).attr('data-steps');

            $(".custom-error").remove();

            var flag = 1;

            if(v == 'confirm'){ //datetime

                $('#sendCodeBtn_txt').html("");

                $('#sendCodeBtn_txt').hide();

                var fullname = $('.fullname').val();

                var email = $('.email').val();

                //var title = $('.title').val();

                var phone = $('.phone').val();

                var description = $('.description').val();

                var timeslot_col_date = $('#timeslot_col_date').val();

                var timeslot_col_time = $('#timeslot_col_time').val();



                // Standardized phone validation regex

                var phoneRegex = /^[0-9]{10,15}$/;

                // Regular expression to allow only letters and spaces (no special characters)

                var nameRegex = /^[a-zA-Z\s]+$/;



                var appointment_item = $('.appointment_item').val();

                if( !$.trim(appointment_item) ){

                    flag = 0;

                    $('.appointment_item').after('<span class="custom-error" role="alert">Appointment detail is required</span>');

                }

                if( !$.trim(fullname) ){

                    flag = 0;

                    $('.fullname').after('<span class="custom-error" role="alert">Fullname is required</span>');

                }

                else if (!nameRegex.test(fullname)) {

                    flag = 0;

                    // Show error message if fullname contains special characters

                    $('.fullname').after('<span class="custom-error" role="alert">Full name must not contain special characters</span>');

                }

                if( !ValidateEmail(email) ){

                    flag = 0;

                    if(!$.trim(email)){

                        $('.email').after('<span class="custom-error" role="alert">Email is required.</span>');

                    }else{

                        $('.email').after('<span class="custom-error" role="alert">You have entered an invalid email address!</span>');

                    }

                }



                if( !$.trim(phone) ){

                    flag = 0;

                    $('#sendCodeBtn').after('<span class="custom-error" role="alert">Phone number is required</span>');

                } else if (!phoneRegex.test(phone)) {

                    flag = 0;

                    // Show standardized error message

                    $('#sendCodeBtn').after('<span class="custom-error" role="alert">Phone number must be 10-15 digits and contain only numbers</span>');

                } else if( $('#phone_verified_bit').val() != "1" ){

                    flag = 0;

                    $('#sendCodeBtn').after('<span class="custom-error" role="alert">Phone number is not verified</span>');

                }



                if( !$.trim(description) ){

                    flag = 0;

                    $('.description').after('<span class="custom-error" role="alert">Description is required</span>');

                }

                if( !$.trim(description) ){

                    flag = 0;

                    $('.description').after('<span class="custom-error" role="alert">Description is required</span>');

                }

                if( !$.trim(timeslot_col_date) || !$.trim(timeslot_col_time)  ){

                    flag = 0;

                    $('.timeslot_col_date_time').after('<span class="custom-error" role="alert">Date and Time is required</span>');

                }

            }/*else if(v == 'confirm'){



            }*/

            //alert('flag=='+flag+'---v=='+v);

            if(flag == 1 && v == 'confirm'){

                $('.confirm_row').show();

                $('#myTab .nav-item .nav-link').addClass('disabled');

                $('#myTab .nav-item #'+v+'-tab').removeClass('disabled');

                $('#myTab a[href="#'+v+'"]').trigger('click');



                $('.full_name').text($('.fullname').val());

                $('.email').text($('.email').val());

                //$('.title').text($('.title').val());

                $('.phone').text($('.phone').val());

                $('.description').text($('.description').val());

                $('.date').text($('input[name="date"]').val());

                $('.time').text($('input[name="time"]').val());

                //$('.date').text($('#timeslot_col_date').val());

                //$('.time').text($('#timeslot_col_time').val());



                if(  $("input[name='radioGroup']:checked").val() == 1 ){ //paid

                    $('.submitappointment_paid').show();

                    $('.submitappointment').hide();

                } else { //free

                    $('.submitappointment').show();

                    $('.submitappointment_paid').hide();

                }

            } else {

                $('.confirm_row').hide();

            }



            function ValidateEmail(inputText) {

                var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

                if(inputText.match(mailformat)) {

                    return true;

                } else {

                    // alert("You have entered an invalid email address!");

                    return false;

                }

            }

        });



        $(document).delegate('.timeslot_col', 'click', function(){

            $('.timeslot_col').removeClass('active');

            $(this).addClass('active');

            var service_id_val = $("input[name='radioGroup']:checked").val(); //alert(service_id_val);

            var fromtime = $(this).attr('data-fromtime');

            if(service_id_val == 2){ //15 min service

                var fromtime11 = parseTimeLatest(fromtime);

                var interval11 = 15;

                var timeString11 = fromtime11 + interval11;

                var totime = new Date('1970-01-01T' + convertHours(timeString11) + 'Z')

                .toLocaleTimeString('en-US',

                    {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}

                );

            } else {

                var totime = $(this).attr('data-totime');

            }

            //alert('totime='+totime);

            $('input[name="time"]').val(fromtime+'-'+totime);

            $('#timeslot_col_time').val(fromtime+'-'+totime);

        });



        function parseTime(s) {

            var c = s.split(':');

            return parseInt(c[0]) * 60 + parseInt(c[1]);

        }



        function parseTimeLatest(s) {

            var c = s.split(':');

            var c11 = c[1].split(' ');

            if(c11[1] == 'PM'){

                if(parseInt(c[0]) != 12 ){

                    return ( parseInt(c[0])+12 ) * 60 + parseInt(c[1]);

                } else {

                    return parseInt(c[0]) * 60 + parseInt(c[1]);

                }

            } else {

                return parseInt(c[0]) * 60 + parseInt(c[1]);

            }

        }



        function convertHours(mins){

            var hour = Math.floor(mins/60);

            var mins = mins%60;

            var converted = pad(hour, 2)+':'+pad(mins, 2);

            return converted;

        }



        function pad (str, max) {

            str = str.toString();

            return str.length < max ? pad("0" + str, max) : str;

        }



        function calculate_time_slot(start_time, end_time, interval = "15"){

            var i, formatted_time;

            var time_slots = new Array();

            for(var i=start_time; i<=end_time; i = i+interval){

                formatted_time = convertHours(i);

                const timeString = formatted_time;



                time_slots.push(timeString);

            }

            return time_slots;

        }





        /////////////////////////////////////////////

        ////// appointment popup chnages end /////////

        /////////////////////////////////////////////



        $('.manual_email_phone_verified').on('change', function(){

            if( $(this).is(":checked") ) {

                $('.manual_email_phone_verified').val(1);

                var manual_email_phone_verified = 1;

            } else {

                $('.manual_email_phone_verified').val(0);

                var manual_email_phone_verified = 0;

            }



            var client_id = window.ClientDetailConfig.clientId; //alert(site_url);

            $.ajax({

                url: site_url+'/clients/update-email-verified',

                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

                type:'POST',

                data:{manual_email_phone_verified:manual_email_phone_verified,client_id:client_id},

                success: function(responses){

                    location.reload();

                }

            });

        });



        //alert('ready');

        $('#feather-icon').click(function(){

            var windowsize = $(window).width(); 

            if($('.main-sidebar').width() == 65){

                if(windowsize > 2000){

                    $('.add_note,.last_updated_date').css('width','980px');

                } else {

                    $('.add_note').css('width','338px');

                    $('.last_updated_date').css('width','348px');

                }



            } else if($('.main-sidebar').width() == 250) {

                if(windowsize > 2000){

                    $('.add_note,.last_updated_date').css('width','1040px');

                } else {

                    $('.add_note').css('width','433px');

                    $('.last_updated_date').css('width','442px');

                }

            }

        });

        //set height of right side section

        var left_upper_height = $('.left_section_upper').height();

        //var left_section_lower = $('.left_section_lower').height();

        var left_section_lower = 0;

        var total_left  = left_upper_height + left_section_lower;

        total_left = total_left +25;



        var right_section_height = $('.right_section').height();

       



        //alert(left_upper_height+'==='+left_section_lower+'==='+total_left+'==='+right_section_height);

        if(right_section_height >total_left ){ 

            var total_left_px = total_left+'px';

            $('.right_section').css({"maxHeight":total_left_px});

            $('.right_section').css({"overflow": 'scroll' });

        } else {  

            var total_left_px = total_left+'px';

            $('.right_section').css({"maxHeight":total_left_px});

        }





        let css_property =

            {

                "display": "none",

            }

        $('#create_note_d').hide();

        $('.main-footer').css(css_property);







        $(document).delegate('.uploadmail','click', function(){

            $('#maclient_id').val(window.ClientDetailConfig.clientId);

            $('#uploadmail').modal('show');

        });



        $(document).delegate('.uploadAndFetchMail','click', function(){

            $('#maclient_id_fetch').val(window.ClientDetailConfig.clientId);

            var hidden_client_matter_id = $('#sel_matter_id_client_detail').val();

            $('#upload_inbox_mail_client_matter_id').val(hidden_client_matter_id);

            $('#uploadAndFetchMailModel').modal('show');

        });

        // Handle uploadAndFetchMail form submission via AJAX
        $(document).on('submit', '#uploadAndFetchMail', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            $('.popuploader').show();
            $('.custom-error-msg').html('');
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('.popuploader').hide();
                    if (response.status) {
                        $('.custom-error-msg').html('<span class="alert alert-success">' + response.message + '</span>');
                        $('#uploadAndFetchMailModel').modal('hide');
                        // Reload the page to show the uploaded emails
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        $('.custom-error-msg').html('<span class="alert alert-danger">' + response.message + '</span>');
                    }
                },
                error: function(xhr) {
                    $('.popuploader').hide();
                    var errorMessage = 'An unexpected error occurred. Please try again.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        var errorHtml = '<span class="alert alert-danger">';
                        for (var field in errors) {
                            errorHtml += errors[field][0] + '<br>';
                        }
                        errorHtml += '</span>';
                        $('.custom-error-msg').html(errorHtml);
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        $('.custom-error-msg').html('<span class="alert alert-danger">' + xhr.responseJSON.message + '</span>');
                    } else {
                        $('.custom-error-msg').html('<span class="alert alert-danger">' + errorMessage + '</span>');
                    }
                }
            });
        });

        // Handle uploadSentAndFetchMail form submission via AJAX
        $(document).on('submit', '#uploadSentAndFetchMail', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            $('.popuploader').show();
            $('.custom-error-msg').html('');
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('.popuploader').hide();
                    if (response.status) {
                        $('.custom-error-msg').html('<span class="alert alert-success">' + response.message + '</span>');
                        $('#uploadSentAndFetchMailModel').modal('hide');
                        // Reload the page to show the uploaded emails
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        $('.custom-error-msg').html('<span class="alert alert-danger">' + response.message + '</span>');
                    }
                },
                error: function(xhr) {
                    $('.popuploader').hide();
                    var errorMessage = 'An unexpected error occurred. Please try again.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        var errorHtml = '<span class="alert alert-danger">';
                        for (var field in errors) {
                            errorHtml += errors[field][0] + '<br>';
                        }
                        errorHtml += '</span>';
                        $('.custom-error-msg').html(errorHtml);
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        $('.custom-error-msg').html('<span class="alert alert-danger">' + xhr.responseJSON.message + '</span>');
                    } else {
                        $('.custom-error-msg').html('<span class="alert alert-danger">' + errorMessage + '</span>');
                    }
                }
            });
        });







        // Set up CSRF token for all AJAX requests

        $.ajaxSetup({

            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            }

        });



        // Handle form submission via AJAX

        $('#createForm956').on('submit', function(e) {

            e.preventDefault();

            $.ajax({

                url: $(this).attr('action'),

                method: 'POST',

                data: $(this).serialize(),

                success: function(response) {

                    if (response.success) {

                        // Hide the modal

                        $('#form956CreateFormModel').modal('hide');

                        // Reload the page to reflect the new data

                        location.reload();

                    }

                },

                error: function(xhr) {

                    $('.custom-error-msg').html('');

                    let errors = xhr.responseJSON.errors;

                    for (let field in errors) {

                        $('.custom-error-msg').append('<p class="text-red-600">' + errors[field][0] + '</p>');

                    }

                }

            });

        });



        // Handle not lodged checkbox

        document.querySelector('input[name="not_lodged"]').addEventListener('change', function() {

            const dateLodgedInput = document.getElementById('date_lodged');

            dateLodgedInput.disabled = this.checked;

            if (this.checked) {

                dateLodgedInput.value = '';

            }

        });



        // Populate agent details when the modal opens

        $(document).delegate('.form956CreateForm', 'click', function() {

            $('#form956_client_id').val(window.ClientDetailConfig.clientId);

            var hidden_client_matter_id = $('#sel_matter_id_client_detail').val();

            $('#form956_client_matter_id').val(hidden_client_matter_id);



            getMigrationAgentDetail(hidden_client_matter_id);

            $('#form956CreateFormModel').modal('show');

        });



        //Get Migration Agent Detail

        function getMigrationAgentDetail(client_matter_id) {

            $.ajax({

                type:'post',

                url: window.ClientDetailConfig.urls.getMigrationAgentDetail,

                sync:true,

                data: {client_matter_id:client_matter_id},

                success: function(response){

                    var obj = $.parseJSON(response);

                    if(obj.agentInfo){

                        $('#agent_id').val(obj.agentInfo.agentId);

                        if(obj.agentInfo.last_name != ''){

                            var agentFullName = obj.agentInfo.first_name+' '+obj.agentInfo.last_name;

                        } else {

                            var agentFullName =  obj.agentInfo.first_name;

                        }

                        $('#agent_name').val(agentFullName);

                        $('#agent_name_label').html(agentFullName);



                        $('#business_name').val(obj.agentInfo.company_name);

                        $('#business_name_label').html(obj.agentInfo.company_name);



                        $('#application_type').val(obj.matterInfo.title);

                        $('#application_type_label').html(obj.matterInfo.title);

                    }

                }

            });

        }



        // Direct AJAX submission for visa agreement

        $(document).delegate('.visaAgreementCreateForm', 'click', function() {

            var client_id = window.ClientDetailConfig.clientId;

            var client_matter_id = $('#sel_matter_id_client_detail').val();



            // First check if cost assignment exists

            $.ajax({

                url: window.ClientDetailConfig.urls.checkCostAssignment,

                type: "POST",

                data: {

                    client_id: client_id,

                    client_matter_id: client_matter_id,

                    _token: $('meta[name="csrf-token"]').attr('content')

                },

                success: function (response) {

                    if (response.exists) {

                        // Get agent details and then submit via AJAX

                        $.ajax({

                            type: 'post',

                            url: window.ClientDetailConfig.urls.getVisaAgreementAgent,

                            data: {client_matter_id: client_matter_id},

                            success: function(agentResponse) {

                                var obj = $.parseJSON(agentResponse);

                                if(obj.agentInfo) {

                                    // Prepare form data for AJAX submission

                                    var formData = {

                                        _token: $('meta[name="csrf-token"]').attr('content'),

                                        client_id: client_id,

                                        client_matter_id: client_matter_id,

                                        agent_id: obj.agentInfo.agentId,

                                        agent_name: obj.agentInfo.last_name != '' ?

                                            obj.agentInfo.first_name + ' ' + obj.agentInfo.last_name :

                                            obj.agentInfo.first_name,

                                        business_name: obj.agentInfo.company_name || ''

                                    };



                                    // Submit via AJAX

                                    $.ajax({

                                        url: window.ClientDetailConfig.urls.generateAgreement,

                                        method: 'POST',

                                        data: formData,

                                        success: function(response) {

                                            // Handle successful response

                                            if (response.success && response.download_url) {

                                                // Use window.open for better download handling

                                                try {

                                                    // Method 1: Try window.open first

                                                    var downloadWindow = window.open(response.download_url, '_blank');

                                                    

                                                    // Fallback: If window.open fails, try direct download

                                                    setTimeout(function() {

                                                        if (!downloadWindow || downloadWindow.closed) {

                                                            // Method 2: Create a form and submit it

                                                            var form = document.createElement('form');

                                                            form.method = 'GET';

                                                            form.action = response.download_url;

                                                            form.target = '_blank';

                                                            document.body.appendChild(form);

                                                            form.submit();

                                                            document.body.removeChild(form);

                                                        }

                                                    }, 1000);

                                                    

                                                    // Show success message

                                                    alert('Visa agreement generated successfully!');

                                                } catch (error) {

                                                    console.error('Download error:', error);

                                                    // Method 3: Direct link approach as last resort

                                                    var link = document.createElement('a');

                                                    link.href = response.download_url;

                                                    link.download = 'visa_agreement_' + new Date().getTime() + '.docx';

                                                    link.target = '_blank';

                                                    document.body.appendChild(link);

                                                    link.click();

                                                    document.body.removeChild(link);

                                                    

                                                    alert('Visa agreement generated successfully!');

                                                }

                                            } else {

                                                alert('Document generated but no download URL returned.');

                                            }

                                        },

                                        error: function(xhr) {

                                            // Handle errors

                                            if (xhr.responseJSON && xhr.responseJSON.message) {

                                                alert('Error: ' + xhr.responseJSON.message);

                                            } else {

                                                alert('Error generating visa agreement.');

                                            }

                                        }

                                    });

                                } else {

                                    alert("Agent information not found.");

                                }

                            },

                            error: function() {

                                alert("Error fetching agent details.");

                            }

                        });

                    } else {

                        alert("Please first create Cost Assignment.");

                    }

                },

                error: function() {

                    alert("Error checking cost assignment.");

                }

            });

        });



         //Get Visa agreement Migration Agent Detail

        function getVisaAggreementMigrationAgentDetail(client_matter_id) {

            $.ajax({

                type:'post',

                url: window.ClientDetailConfig.urls.getVisaAgreementAgent,

                sync:true,

                data: {client_matter_id:client_matter_id},

                success: function(response){

                    var obj = $.parseJSON(response);

                    if(obj.agentInfo){

                        $('#visaagree_agent_id').val(obj.agentInfo.agentId);

                        if(obj.agentInfo.last_name != ''){

                            var agentFullName = obj.agentInfo.first_name+' '+obj.agentInfo.last_name;

                        } else {

                            var agentFullName =  obj.agentInfo.first_name;

                        }

                        $('#visaagree_agent_name').val(agentFullName);

                        $('#visaagree_agent_name_label').html(agentFullName);



                        $('#visaagree_business_name').val(obj.agentInfo.company_name);

                        $('#visaagree_business_name_label').html(obj.agentInfo.company_name);

                    }

                }

            });

        }



        // Handle form submission via AJAX

        $('#visaagreementform11').on('submit', function(e) {

            e.preventDefault();

            let form = $(this);

            $.ajax({

                url: form.attr('action'),

                method: 'POST',

                data: form.serialize(),

                success: function(response) {

                    // Hide modal if needed

                    $('#visaAgreementCreateFormModel').modal('hide');



                    // Redirect to download URL

                    if (response.download_url) {

                        window.location.href = response.download_url;

                    } else {

                        alert('Document generated but no download URL returned.');

                    }

                },

                error: function(xhr) {

                    $('.custom-error-msg').html('');

                    let errors = xhr.responseJSON?.errors || {};

                    for (let field in errors) {

                        $('.custom-error-msg').append('<p class="text-red-600">' + errors[field][0] + '</p>');

                    }

                }

            });

        });





        // Populate agent details when the modal opens

        $(document).delegate('.costAssignmentCreateForm', 'click', function() {

            $('#cost_assignment_client_id').val(window.ClientDetailConfig.clientId);

            var hidden_client_matter_id_assignment = $('#sel_matter_id_client_detail').val();

            $('#cost_assignment_client_matter_id').val(hidden_client_matter_id_assignment);

            getCostAssignmentMigrationAgentDetail(window.ClientDetailConfig.clientId, hidden_client_matter_id_assignment);

            $('#costAssignmentCreateFormModel').modal('show');

        });



         //Get Cost assignment Migration Agent Detail

        function getCostAssignmentMigrationAgentDetail(client_id,client_matter_id) {

            $.ajax({

                type:'post',

                url: window.ClientDetailConfig.urls.getCostAssignmentAgent,

                sync:true,

                data: {client_id:client_id,client_matter_id:client_matter_id},

                success: function(response){

                    var obj = $.parseJSON(response);

                    if(obj.agentInfo){

                        $('#costassign_agent_id').val(obj.agentInfo.agentId);

                        if(obj.agentInfo.last_name != ''){

                            var agentFullName = obj.agentInfo.first_name+' '+obj.agentInfo.last_name;

                        } else {

                            var agentFullName =  obj.agentInfo.first_name;

                        }

                        //$('#costassign_agent_name').val(agentFullName);

                        $('#costassign_agent_name_label').html(agentFullName);



                        //$('#costassign_business_name').val(obj.agentInfo.company_name);

                        $('#costassign_business_name_label').html(obj.agentInfo.company_name);

                        $('#costassign_client_matter_name_label').html(obj.matterInfo.title);



                        //Fetch matter related cost assignments

                        if(obj.cost_assignment_matterInfo){

                            $('#surcharge').val(obj.cost_assignment_matterInfo.surcharge);

                            $('#Dept_Base_Application_Charge').val(obj.cost_assignment_matterInfo.Dept_Base_Application_Charge);

                            $('#Dept_Base_Application_Charge_no_of_person').val(obj.cost_assignment_matterInfo.Dept_Base_Application_Charge_no_of_person);



                            $('#Dept_Non_Internet_Application_Charge').val(obj.cost_assignment_matterInfo.Dept_Non_Internet_Application_Charge);

                            $('#Dept_Non_Internet_Application_Charge_no_of_person').val(obj.cost_assignment_matterInfo.Dept_Non_Internet_Application_Charge_no_of_person);



                            $('#Dept_Additional_Applicant_Charge_18_Plus').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_18_Plus);

                            $('#Dept_Additional_Applicant_Charge_18_Plus_no_of_person').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_18_Plus_no_of_person);



                            $('#Dept_Additional_Applicant_Charge_Under_18').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_Under_18);

                            $('#Dept_Additional_Applicant_Charge_Under_18_no_of_person').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_Under_18_no_of_person);



                            $('#Dept_Subsequent_Temp_Application_Charge').val(obj.cost_assignment_matterInfo.Dept_Subsequent_Temp_Application_Charge);

                            $('#Dept_Subsequent_Temp_Application_Charge_no_of_person').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_Under_18_no_of_person);



                            $('#Dept_Second_VAC_Instalment_Charge_18_Plus').val(obj.cost_assignment_matterInfo.Dept_Second_VAC_Instalment_Charge_18_Plus);

                            $('#Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person').val(obj.cost_assignment_matterInfo.Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person);



                            $('#Dept_Second_VAC_Instalment_Under_18').val(obj.cost_assignment_matterInfo.Dept_Second_VAC_Instalment_Under_18);

                            $('#Dept_Second_VAC_Instalment_Under_18_no_of_person').val(obj.cost_assignment_matterInfo.Dept_Second_VAC_Instalment_Under_18_no_of_person);



                            $('#Dept_Nomination_Application_Charge').val(obj.cost_assignment_matterInfo.Dept_Nomination_Application_Charge);

                            $('#Dept_Sponsorship_Application_Charge').val(obj.cost_assignment_matterInfo.Dept_Sponsorship_Application_Charge);



                            $('#TotalDoHACharges').val(obj.cost_assignment_matterInfo.TotalDoHACharges);

                            $('#TotalDoHASurcharges').val(obj.cost_assignment_matterInfo.TotalDoHASurcharges);



                            $('#Block_1_Ex_Tax').val(obj.cost_assignment_matterInfo.Block_1_Ex_Tax);

                            $('#Block_2_Ex_Tax').val(obj.cost_assignment_matterInfo.Block_2_Ex_Tax);

                            $('#Block_3_Ex_Tax').val(obj.cost_assignment_matterInfo.Block_3_Ex_Tax);



                            $('#additional_fee_1').val(obj.cost_assignment_matterInfo.additional_fee_1);

                            $('#TotalBLOCKFEE').val(obj.cost_assignment_matterInfo.TotalBLOCKFEE);

                        } else {

                            $('#surcharge').val(obj.matterInfo.surcharge);

                            $('#Dept_Base_Application_Charge').val(obj.matterInfo.Dept_Base_Application_Charge);

                            $('#Dept_Non_Internet_Application_Charge').val(obj.matterInfo.Dept_Non_Internet_Application_Charge);

                            $('#Dept_Additional_Applicant_Charge_18_Plus').val(obj.matterInfo.Dept_Additional_Applicant_Charge_18_Plus);

                            $('#Dept_Additional_Applicant_Charge_Under_18').val(obj.matterInfo.Dept_Additional_Applicant_Charge_Under_18);

                            $('#Dept_Subsequent_Temp_Application_Charge').val(obj.matterInfo.Dept_Subsequent_Temp_Application_Charge);

                            $('#Dept_Second_VAC_Instalment_Charge_18_Plus').val(obj.matterInfo.Dept_Second_VAC_Instalment_Charge_18_Plus);

                            $('#Dept_Second_VAC_Instalment_Under_18').val(obj.matterInfo.Dept_Second_VAC_Instalment_Under_18);

                            $('#Dept_Nomination_Application_Charge').val(obj.matterInfo.Dept_Nomination_Application_Charge);

                            $('#Dept_Sponsorship_Application_Charge').val(obj.matterInfo.Dept_Sponsorship_Application_Charge);



                            $('#Block_1_Ex_Tax').val(obj.matterInfo.Block_1_Ex_Tax);

                            $('#Block_2_Ex_Tax').val(obj.matterInfo.Block_2_Ex_Tax);

                            $('#Block_3_Ex_Tax').val(obj.matterInfo.Block_3_Ex_Tax);



                            $('#additional_fee_1').val(obj.matterInfo.additional_fee_1);

                            $('#TotalBLOCKFEE').val(obj.matterInfo.TotalBLOCKFEE);

                            $('#TotalDoHACharges').val(obj.matterInfo.TotalDoHACharges);

                            $('#TotalDoHASurcharges').val(obj.matterInfo.TotalDoHASurcharges);

                        }

                    }

                }

            });

        }



        // Handle form submission via AJAX

        $(document).on('submit', '#costAssignmentform', function(e) {

            e.preventDefault();

            $.ajax({

                url: $(this).attr('action'),

                method: 'POST',

                data: $(this).serialize(),

                dataType: 'json',

                success: function(response) {

                    // Check if response is already an object or needs parsing

                    var obj = typeof response === 'string' ? $.parseJSON(response) : response;

                    if (obj.status) {

                        // Hide the modal

                        $('#costAssignmentCreateFormModel').modal('hide');

                        localStorage.setItem('activeTab', 'formgenerations');

                        // Reload the page to reflect the new data

                        location.reload();

                    }

                },

                error: function(xhr, status, error) {

                    //console.log('Error:', xhr, status, error); // Debug log

                    $('.custom-error-msg').html('');

                    if (xhr.responseJSON && xhr.responseJSON.errors) {

                        let errors = xhr.responseJSON.errors;

                        for (let field in errors) {

                            $('.custom-error-msg').append('<p class="text-red-600">' + errors[field][0] + '</p>');

                        }

                    } else {

                        $('.custom-error-msg').append('<p class="text-red-600">An error occurred while submitting the form.</p>');

                    }

                }

            });

        });



        // Alternative approach: Handle button click directly

        $(document).on('click', '#costAssignmentform button[type="submit"]', function(e) {

            e.preventDefault();

            var form = $('#costAssignmentform');

            $.ajax({

                url: form.attr('action'),

                method: 'POST',

                data: form.serialize(),

                dataType: 'json',

                success: function(response) {

                    //console.log('Success response:', response); // Debug log

                    // Check if response is already an object or needs parsing

                    var obj = typeof response === 'string' ? $.parseJSON(response) : response;

                    if (obj.status) {

                        // Hide the modal

                        $('#costAssignmentCreateFormModel').modal('hide');

                        localStorage.setItem('activeTab', 'formgenerations');

                        // Reload the page to reflect the new data

                        location.reload();

                    }

                },

                error: function(xhr, status, error) {

                    //console.log('Error:', xhr, status, error); // Debug log

                    $('.custom-error-msg').html('');

                    if (xhr.responseJSON && xhr.responseJSON.errors) {

                        let errors = xhr.responseJSON.errors;

                        for (let field in errors) {

                            $('.custom-error-msg').append('<p class="text-red-600">' + errors[field][0] + '</p>');

                        }

                    } else {

                        $('.custom-error-msg').append('<p class="text-red-600">An error occurred while submitting the form.</p>');

                    }

                }

            });

        });



        //Lead Section Start

            // Populate agent details when the modal opens

            $(document).delegate('.costAssignmentCreateFormLead', 'click', function() {

                $('#cost_assignment_lead_id').val(window.ClientDetailConfig.clientId);

                $('#sel_migration_agent_id_lead,#sel_person_responsible_id_lead,#sel_person_assisting_id_lead,#sel_matter_id_lead').select2({

                    dropdownParent: $('#costAssignmentCreateFormModelLead')

                });

                $('#costAssignmentCreateFormModelLead').modal('show');

            });



            $(document).delegate('#sel_matter_id_lead','change', function(){

                var client_matter_id = $(this).val();

                var client_id = window.ClientDetailConfig.clientId;

                if (client_id && client_matter_id) {

                    getCostAssignmentMigrationAgentDetailLead(client_id, client_matter_id);

                }

            });



            //Get Cost assignment Migration Agent Detail

            function getCostAssignmentMigrationAgentDetailLead(client_id,client_matter_id) {

                $.ajax({

                    type:'post',

                    url: window.ClientDetailConfig.urls.getCostAssignmentAgentLead,

                    sync:true,

                    data: {client_id:client_id,client_matter_id:client_matter_id},

                    success: function(response){

                        var obj = $.parseJSON(response);

                        //Fetch matter related cost assignments

                        if(obj.cost_assignment_matterInfo){

                            $('#surcharge_lead').val(obj.cost_assignment_matterInfo.surcharge);

                            $('#Dept_Base_Application_Charge_lead').val(obj.cost_assignment_matterInfo.Dept_Base_Application_Charge);

                            $('#Dept_Base_Application_Charge_no_of_person_lead').val(obj.cost_assignment_matterInfo.Dept_Base_Application_Charge_no_of_person);



                            $('#Dept_Non_Internet_Application_Charge_lead').val(obj.cost_assignment_matterInfo.Dept_Non_Internet_Application_Charge);

                            $('#Dept_Non_Internet_Application_Charge_no_of_person_lead').val(obj.cost_assignment_matterInfo.Dept_Non_Internet_Application_Charge_no_of_person);



                            $('#Dept_Additional_Applicant_Charge_18_Plus_lead').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_18_Plus);

                            $('#Dept_Additional_Applicant_Charge_18_Plus_no_of_person_lead').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_18_Plus_no_of_person);



                            $('#Dept_Additional_Applicant_Charge_Under_18_lead').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_Under_18);

                            $('#Dept_Additional_Applicant_Charge_Under_18_no_of_person_lead').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_Under_18_no_of_person);



                            $('#Dept_Subsequent_Temp_Application_Charge_lead').val(obj.cost_assignment_matterInfo.Dept_Subsequent_Temp_Application_Charge);

                            $('#Dept_Subsequent_Temp_Application_Charge_no_of_person_lead').val(obj.cost_assignment_matterInfo.Dept_Additional_Applicant_Charge_Under_18_no_of_person);



                            $('#Dept_Second_VAC_Instalment_Charge_18_Plus_lead').val(obj.cost_assignment_matterInfo.Dept_Second_VAC_Instalment_Charge_18_Plus);

                            $('#Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person_lead').val(obj.cost_assignment_matterInfo.Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person);



                            $('#Dept_Second_VAC_Instalment_Under_18_lead').val(obj.cost_assignment_matterInfo.Dept_Second_VAC_Instalment_Under_18);

                            $('#Dept_Second_VAC_Instalment_Under_18_no_of_person_lead').val(obj.cost_assignment_matterInfo.Dept_Second_VAC_Instalment_Under_18_no_of_person);



                            $('#Dept_Nomination_Application_Charge_lead').val(obj.cost_assignment_matterInfo.Dept_Nomination_Application_Charge);

                            $('#Dept_Sponsorship_Application_Charge_lead').val(obj.cost_assignment_matterInfo.Dept_Sponsorship_Application_Charge);



                            $('#TotalDoHACharges_lead').val(obj.cost_assignment_matterInfo.TotalDoHACharges);

                            $('#TotalDoHASurcharges_lead').val(obj.cost_assignment_matterInfo.TotalDoHASurcharges);



                            $('#Block_1_Ex_Tax_lead').val(obj.cost_assignment_matterInfo.Block_1_Ex_Tax);

                            $('#Block_2_Ex_Tax_lead').val(obj.cost_assignment_matterInfo.Block_2_Ex_Tax);

                            $('#Block_3_Ex_Tax_lead').val(obj.cost_assignment_matterInfo.Block_3_Ex_Tax);



                            $('#additional_fee_1_lead').val(obj.cost_assignment_matterInfo.additional_fee_1);

                            $('#TotalBLOCKFEE_lead').val(obj.cost_assignment_matterInfo.TotalBLOCKFEE);

                        }

                        else {

                            $('#surcharge_lead').val(obj.matterInfo.surcharge);

                            $('#Dept_Base_Application_Charge_lead').val(obj.matterInfo.Dept_Base_Application_Charge);

                            $('#Dept_Non_Internet_Application_Charge_lead').val(obj.matterInfo.Dept_Non_Internet_Application_Charge);

                            $('#Dept_Additional_Applicant_Charge_18_Plus_lead').val(obj.matterInfo.Dept_Additional_Applicant_Charge_18_Plus);

                            $('#Dept_Additional_Applicant_Charge_Under_18_lead').val(obj.matterInfo.Dept_Additional_Applicant_Charge_Under_18);

                            $('#Dept_Subsequent_Temp_Application_Charge_lead').val(obj.matterInfo.Dept_Subsequent_Temp_Application_Charge);

                            $('#Dept_Second_VAC_Instalment_Charge_18_Plus_lead').val(obj.matterInfo.Dept_Second_VAC_Instalment_Charge_18_Plus);

                            $('#Dept_Second_VAC_Instalment_Under_18_lead').val(obj.matterInfo.Dept_Second_VAC_Instalment_Under_18);

                            $('#Dept_Nomination_Application_Charge_lead').val(obj.matterInfo.Dept_Nomination_Application_Charge);

                            $('#Dept_Sponsorship_Application_Charge_lead').val(obj.matterInfo.Dept_Sponsorship_Application_Charge);



                            $('#Block_1_Ex_Tax_lead').val(obj.matterInfo.Block_1_Ex_Tax);

                            $('#Block_2_Ex_Tax_lead').val(obj.matterInfo.Block_2_Ex_Tax);

                            $('#Block_3_Ex_Tax_lead').val(obj.matterInfo.Block_3_Ex_Tax);



                            $('#additional_fee_1_lead').val(obj.matterInfo.additional_fee_1);

                            $('#TotalBLOCKFEE_lead').val(obj.matterInfo.TotalBLOCKFEE);

                            $('#TotalDoHACharges_lead').val(obj.matterInfo.TotalDoHACharges);

                            $('#TotalDoHASurcharges_lead').val(obj.matterInfo.TotalDoHASurcharges);

                        }

                    }

                });

            }

        //Lead Section End



        //Open Agreement model window

        $(document).delegate('.finalizeAgreementConvertToPdf', 'click', function() {

            var hidden_client_matter_id_assignment = $('#sel_matter_id_client_detail').val();

            $('#agreemnt_clientmatterid').val(hidden_client_matter_id_assignment);

            $('#agreementModal').modal('show');

        });



        $(document).on('submit', '#agreementUploadForm', function(e) {

            e.preventDefault();

            var formData = new FormData(this);

            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.uploadAgreement,

                type: 'POST',

                data: formData,

                processData: false,

                contentType: false,

                headers: {'X-CSRF-TOKEN': window.ClientDetailConfig.csrfToken},

                success: function(response) {

                    $('.popuploader').hide();

                    if(response.status){

                        $('#agreementModal').modal('hide');

                        $('.custom-error-msg').html('<span class="alert alert-success">'+response.message+'</span>');

                        localStorage.setItem('activeTab', 'formgenerations');

                        location.reload();

                    } else {

                        $('.custom-error-msg').html('<span class="alert alert-danger">'+response.message+'</span>');

                    }

                },

                error: function(xhr, status, error) {

                    $('.popuploader').hide();

                    if(xhr.responseJSON && xhr.responseJSON.message) {

                        $('.custom-error-msg').html('<span class="alert alert-danger">Error: ' + xhr.responseJSON.message + '</span>');

                    } else {

                        $('.custom-error-msg').html('<span class="alert alert-danger">An error occurred while uploading the agreement.</span>');

                    }

                }

            });

        });



        // Backup click handler for the submit button

        $(document).on('click', '#agreementUploadForm button[type="submit"]', function(e) {

            e.preventDefault();

            var form = $('#agreementUploadForm')[0];

            var formData = new FormData(form);

            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.uploadAgreement,

                type: 'POST',

                data: formData,

                processData: false,

                contentType: false,

                headers: {'X-CSRF-TOKEN': window.ClientDetailConfig.csrfToken},

                success: function(response) {

                    $('.popuploader').hide();

                    if(response.status){

                        $('#agreementModal').modal('hide');

                        $('.custom-error-msg').html('<span class="alert alert-success">'+response.message+'</span>');

                        localStorage.setItem('activeTab', 'formgenerations');

                        location.reload();

                    } else {

                        $('.custom-error-msg').html('<span class="alert alert-danger">'+response.message+'</span>');

                    }

                },

                error: function(xhr, status, error) {

                    $('.popuploader').hide();

                    if(xhr.responseJSON && xhr.responseJSON.message) {

                        $('.custom-error-msg').html('<span class="alert alert-danger">Error: ' + xhr.responseJSON.message + '</span>');

                    } else {

                        $('.custom-error-msg').html('<span class="alert alert-danger">An error occurred while uploading the agreement.</span>');

                    }

                }

            });

        });



        $(document).delegate('.uploadSentAndFetchMail','click', function(){

            $('#maclient_id_fetch_sent').val(window.ClientDetailConfig.clientId);

            var hidden_client_matter_id = $('#sel_matter_id_client_detail').val();

            $('#upload_sent_mail_client_matter_id').val(hidden_client_matter_id);

            $('#uploadSentAndFetchMailModel').modal('show');

        });



        $(document).delegate('.addnewprevvisa','click', function(){

            var $clone = $('.multiplevisa:eq(0)').clone(true,true);



            $clone.find('.lastfiledcol').after('<div class="col-md-4"><a href="javascript:;" class="removenewprevvisa btn btn-danger btn-sm">Remove</a></div>');

            $clone.find("input:text").val("");

            $clone.find("input.visadatesse").val("");

            $('.multiplevisa:last').after($clone);

        });



        $('#note_deadline_checkbox').on('click', function() {

            if ($(this).is(':checked')) {

                $('#note_deadline').prop('disabled', false);

                $('#note_deadline_checkbox').val(1);

            } else {

                $('#note_deadline').prop('disabled', true);

                $('#note_deadline_checkbox').val(0);

            }

        });



        $('#noteType').on('change', function() {

            var selectedValue = $(this).val();

            var additionalFields = $("#additionalFields");



            // Clear any existing fields

            additionalFields.html("");



            if(selectedValue === "Call") {

                additionalFields.append(`

                    <div class="form-group" style="margin-top:10px;">

                        <label for="mobileNumber">Mobile Number:</label>

                        <select name="mobileNumber" id="mobileNumber" class="form-control" data-valid="required"></select>

                        <span id="mobileNumberError" class="text-danger"></span>

                    </div>

                `);



                //Fetch all contact list of any client at create note popup

                var client_id = $('#client_id').val();

                $('.popuploader').show();

                $.ajax({

                    url: window.ClientDetailConfig.urls.fetchClientContactNo,

                    method: "POST",

                    data: {client_id:client_id},

                    datatype: 'json',

                    success: function(response) {

                        $('.popuploader').hide();

                        var obj = $.parseJSON(response);

                        var contactlist = '<option value="">Select Contact</option>';

                        $.each(obj.clientContacts, function(index, subArray) {

                            contactlist += '<option value="'+subArray.phone+'">'+subArray.phone+'</option>';

                        });

                        $('#mobileNumber').append(contactlist);

                    }

                });

            }

        });





        var activeLink = $('.nav-link.active');

        if (activeLink.length > 0) {

            var href = activeLink.attr('href');

            if(href == '#activities' ) {

                $('.filter_btn').css('display','inline-block');

                $('.filter_panel').css('display','none');

            } else {

                $('.filter_btn,.filter_panel').css('display','none');

            }

        } else {

            $('.filter_btn,.filter_panel').css('display','none');

        }





        $(document).delegate('.nav-link','click', function(){

            var activeLink = $('.nav-link.active');

            if (activeLink.length > 0) {

                var href = activeLink.attr('href');

                if(href == '#activities' ) {

                    $('.filter_btn').css('display','inline-block');

                    $('.filter_panel').css('display','none');

                } else {

                    $('.filter_btn,.filter_panel').css('display','none');

                }

            } else {

                $('.filter_btn,.filter_panel').css('display','none');

            }

        });



        /*$(document).delegate('.btn-assignuser','click', function(){

            var note_description = $('#note_description').val();

            // Remove <p> tags using regex

            var cleanedText = note_description.replace(/<\/?p>/g, '');

            // cleanedText = cleanedText.replace(/<\/?p>/g, '');

            $('#assignnote').val(cleanedText);

        });*/



        $(document).delegate('.removenewprevvisa','click', function(){

            $(this).parent().parent().parent().remove();

        });



        // assignUser function is now handled in addclientmodal.blade.php to avoid conflicts

        $(document).on('click', '#assignUser', function(e) {

            e.preventDefault();

            e.stopPropagation();

            

            $(".popuploader").show();

            let flag = true;

            let error = "";

            $(".custom-error").remove();



            // Get all checked assignee IDs from checkboxes

            let selectedAssignees = [];

            $('.checkbox-item:checked').each(function() {

                selectedAssignees.push($(this).val());

            });

            

            var selectedValues = selectedAssignees; // Use the checked values

            

            // Validation - Check if at least one assignee is selected

            if (selectedAssignees.length === 0) {

                $('.popuploader').hide();

                error = "At least one assignee must be selected.";

                $('#dropdownMenuButton').after("<span class='custom-error' role='alert' style='color: red; font-size: 12px; display: block; margin-top: 5px;'>" + error + "</span>");

                flag = false;

            }

            

            // Check if assignnote field is empty (handle both regular textarea and Summernote)

            var assignnoteValue = '';

            if ($('#assignnote').hasClass('summernote-simple') || $('#assignnote').data('summernote')) {

                assignnoteValue = $('#assignnote').summernote('code');

            } else {

                assignnoteValue = $('#assignnote').val();

            }

            

            if (assignnoteValue.trim() === '') {

                $('.popuploader').hide();

                error = "Note field is required.";

                $('#assignnote').after("<span class='custom-error' role='alert' style='color: red; font-size: 12px; display: block; margin-top: 5px;'>" + error + "</span>");

                flag = false;

            }

            

            if ($('#task_group').val() === '') {

                $('.popuploader').hide();

                error = "Group field is required.";

                $('#task_group').after("<span class='custom-error' role='alert' style='color: red; font-size: 12px; display: block; margin-top: 5px;'>" + error + "</span>");

                flag = false;

            }



            if (flag) {

                $.ajax({

                    type: 'POST',

                    url: window.ClientDetailConfig.urls.followupStore,

                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

                    data: {

                        note_type: 'follow_up',

                        description: assignnoteValue,

                        client_id: $('#assign_client_id').val(),

                        followup_datetime: $('#popoverdatetime').val(),

                        rem_cat: selectedValues,

                        task_group: $('#task_group option:selected').val(),

                        note_deadline_checkbox: $('#note_deadline_checkbox').val(),

                        note_deadline: $('#note_deadline').val()

                    },

                    success: function(response) {

                        $('.popuploader').hide();

                        $('#create_action_popup').modal('hide');

                        var obj = $.parseJSON(response);

                        if (obj.success) {

                            $("[data-role=popover]").each(function() {

                                (($(this).popover('hide').data('bs.popover') || {}).inState || {}).click = false; // fix for BS 3.3.6

                            });

                            

                            // Reset form fields after successful submission

                            $('#assignnote').val('');

                            $('#task_group').val('');

                            $('#popoverdatetime').val((new Date().toISOString().split('T')[0]));

                            $('#note_deadline').val((new Date().toISOString().split('T')[0]));

                            $('#note_deadline_checkbox').prop('checked', false);

                            $('#note_deadline').prop('disabled', true);

                            $('.checkbox-item').prop('checked', false);

                            

                            // Reset assignee selection

                            if (typeof updateSelectedUsers === 'function') {

                                updateSelectedUsers();

                            }

                            if (typeof updateHiddenSelect === 'function') {

                                updateHiddenSelect();

                            }

                            

                            // Call the functions to refresh the data

                            if (typeof getallactivities === 'function') {

                                getallactivities();

                            }

                            if (typeof getallnotes === 'function') {

                                getallnotes();

                            }

                        } else {

                            // Handle failure

                            alert('Error: ' + (obj.message || 'Something went wrong'));

                        }

                    },

                    error: function(xhr, status, error) {

                        $('.popuploader').hide();

                        console.error('Ajax error:', error);

                        alert('Error: ' + error);

                    }

                });

            } else {

                $('.popuploader').hide();

            }

        });



        $(document).delegate('.opentaskview', 'click', function(){

            $('#opentaskview').modal('show');

            var v = $(this).attr('id');

            $.ajax({

                url: site_url+'/get-task-detail',

                type:'GET',

                data:{task_id:v},

                success: function(responses){



                    $('.taskview').html(responses);

                }

            });

        });



        function getallnotes(){

            $.ajax({

                url: site_url+'/get-notes',

                type:'GET',

                data:{clientid:window.ClientDetailConfig.clientId,type:'client'},

                success: function(responses){

                    $('.popuploader').hide();

                    $('.note_term_list').html(responses);

                    if ($('.general_matter_checkbox_client_detail').is(':checked')) {

                        selectedMatter = $('.general_matter_checkbox_client_detail').val();

                    } else {

                        selectedMatter = $('#sel_matter_id_client_detail').val();

                    }

                    // Apply combined matter and task group filtering
                    const activeTaskGroup = $('.subtab8-button.active').data('subtab8') || 'All';
                    
                    // If no tab is active, make "All" tab active and show all notes
                    if (!$('.subtab8-button.active').length) {
                        $('.subtab8-button.pill-tab[data-subtab8="All"]').addClass('active');
                        // Show all notes when no tab is active (initial load)
                        $('#noteterm-tab').find('.note-card-redesign').show();
                    } else {
                        // Apply filtering only when a tab is already active
                        $('#noteterm-tab').find('.note-card-redesign').each(function() {
                            const noteMatterId = $(this).data('matterid');
                            const noteType = $(this).data('type');
                            
                            let showNote = false;
                            
                            // Matter filtering logic
                            if (selectedMatter !== "") {
                                // Show notes that match the selected matter OR notes with no matter_id
                                showNote = (noteMatterId == selectedMatter || noteMatterId == '' || noteMatterId == null);
                            } else {
                                // Show ALL notes when no matter is selected
                                showNote = true;
                            }
                            
                            // Task group filtering logic
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

                    

                    // Adjust Activity Feed height after content update

                    adjustActivityFeedHeight();

                }

            });

        }



        

        function getallactivities(){

            $.ajax({

                url: site_url+'/get-activities',

                type:'GET',

                datatype:'json',

                data:{id:window.ClientDetailConfig.clientId},

                success: function(responses){

                    var ress = JSON.parse(responses);

                    var html = '';



                    $.each(ress.data, function (k, v) {

                        // Determine icon based on activity type
                        var activityType = v.activity_type ?? 'note';
                        var subjectIcon;
                        var iconClass = '';
                        
                        if (activityType === 'sms') {
                            subjectIcon = '<i class="fas fa-sms"></i>';
                            iconClass = 'feed-icon-sms';
                        } else if (v.subject && v.subject.toLowerCase().includes("document")) {
                            subjectIcon = '<i class="fas fa-file-alt"></i>';
                        } else {
                            subjectIcon = '<i class="fas fa-sticky-note"></i>';
                        }

                        var subject = v.subject ?? '';

                        var description = v.message ?? '';

                        var taskGroup = v.task_group ?? '';

                        var followupDate = v.followup_date ?? '';

                        var date = v.date ?? '';

                        var createdBy = v.createdname ?? 'Unknown';

                        var fullName = v.name ?? '';
                        
                        var activityTypeClass = activityType ? 'activity-type-' + activityType : '';

                        html += `

                            <li class="feed-item feed-item--email activity ${activityTypeClass}" id="activity_${v.activity_id}">

                                <span class="feed-icon ${iconClass}">

                                    ${subjectIcon}

                                </span>

                                <div class="feed-content">

                                    <p><strong>${fullName} ${subject}</strong></p>

                                    ${description !== '' ? `<p>${description}</p>` : ''}

                                    ${taskGroup !== '' ? `<p>${taskGroup}</p>` : ''}

                                    ${followupDate !== '' ? `<p>${followupDate}</p>` : ''}

                                    <span class="feed-timestamp">${date}</span>

                                </div>

                            </li>

                        `;

                    });



                    $('.feed-list').html(html);

                    //$('.activities').html(html);

                    $('.popuploader').hide();

                    

                    // Adjust Activity Feed height after content update

                    adjustActivityFeedHeight();

                }

            });

        }



        var appcid = '';

        $(document).delegate('.publishdoc', 'click', function(){

            $('#confirmpublishdocModal').modal('show');

            appcid = $(this).attr('data-id');

        });



        $(document).delegate('.openassigneeshow', 'click', function(){

            $('.assigneeshow').show();

        });



        $(document).delegate('.closeassigneeshow', 'click', function(){

            $('.assigneeshow').hide();

        });



        $(document).delegate('.saveassignee', 'click', function(){

            var appliid = $(this).attr('data-id');

            $('.popuploader').show();

            $.ajax({

                url: site_url+'/clients/change_assignee',

                type:'GET',

                data:{id: appliid,assinee: $('#changeassignee').val()},

                success: function(response){

                    var obj = $.parseJSON(response);

                    if(obj.status){

                        alert(obj.message);

                        location.reload();

                    }else{

                        alert(obj.message);

                    }

                }

            });

        });



        $(document).delegate('#confirmpublishdocModal .acceptpublishdoc', 'click', function(){

            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.publishDoc,

                type:'GET',

                datatype:'json',

                data:{appid:appcid,status:'1'},

                success:function(response){

                    $('.popuploader').hide();

                    var res = JSON.parse(response);

                    $('#confirmpublishdocModal').modal('hide');

                    if(res.status){

                        $('.mychecklistdocdata').html(res.doclistdata);

                    }else{

                        alert(res.message);

                    }

                }

            });

        });







        var notuse_doc_id = '';

        var notuse_doc_href = '';

        var notuse_doc_type = '';



        // Move the notuseddoc click handler inside document ready

        $(document).on('click', '.notuseddoc', function(e){

            e.preventDefault();

            

            

            // Check if modal exists

            if($('#confirmNotUseDocModal').length === 0) {

                console.error('Modal #confirmNotUseDocModal not found!');

                return;

            }

            

            $('#confirmNotUseDocModal').modal('show');

            notuse_doc_id = $(this).attr('data-id');

            notuse_doc_href = $(this).attr('data-href');

            notuse_doc_type = $(this).attr('data-doctype');

            

        });



        // Alternative approach using delegate for better compatibility

        $(document).delegate('.notuseddoc', 'click', function(e){

            e.preventDefault();

            

            // Check if modal exists

            if($('#confirmNotUseDocModal').length === 0) {

                console.error('Modal #confirmNotUseDocModal not found!');

                return;

            }

            

            $('#confirmNotUseDocModal').modal('show');

            notuse_doc_id = $(this).attr('data-id');

            notuse_doc_href = $(this).attr('data-href');

            notuse_doc_type = $(this).attr('data-doctype');

        });



        // Test if elements with .notuseddoc class exist

        $('.notuseddoc').each(function(index) {

            // Add a test click handler to see if the element is clickable

            $(this).css('cursor', 'pointer');

        });



        // Additional fallback - bind directly to existing elements

        $('.notuseddoc').off('click').on('click', function(e) {

            e.preventDefault();

            e.stopPropagation();

            $('#confirmNotUseDocModal').modal('show');

            notuse_doc_id = $(this).attr('data-id');

            notuse_doc_href = $(this).attr('data-href');

            notuse_doc_type = $(this).attr('data-doctype');

        });



        $(document).delegate('#confirmNotUseDocModal .accept', 'click', function(){

            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.admin + '/documents/not-used',

                type:'POST',

                datatype:'json',

                data:{doc_id:notuse_doc_id, doc_type:notuse_doc_type },

                success:function(response){

                    $('.popuploader').hide();

                    var res = JSON.parse(response);

                    $('#confirmNotUseDocModal').modal('hide');

                    if(res.status){

                        if(res.doc_type == 'personal') {

                            $('.documnetlist_'+res.doc_category+' #id_'+res.doc_id).remove();

                        } else if( res.doc_type == 'visa') {

                            $('.migdocumnetlist1 #id_'+res.doc_id).remove();

                        }

                        localStorage.setItem('activeTab', 'documentalls');

                        location.reload();

                        /*if(res.docInfo) {

                            var subArray = res.docInfo;

                            var trRow = "";

                            trRow += "<tr class='drow' id='id_"+subArray.id+"'><td>"+subArray.checklist+"</td><td>"+subArray.doc_type+"</td><td>"+res.Added_By+"</td><td>"+res.Added_date+"</td><td><i class='fas fa-file-image'></i> <span>"+subArray.file_name+'.'+subArray.filetype+"</span></div></td><td>"+res.Verified_By+"</td><td>"+res.Verified_At+"</td></tr>";

                            $('.notuseddocumnetlist').append(trRow);

                        }*/

                        getallactivities();

                    }

                },

                error: function(xhr, status, error) {

                    $('.popuploader').hide();

                }

            });

        });





        var backto_doc_id = '';

        var backto_doc_href = '';

        var backto_doc_type = '';

        $('.backtodoc').off('click').on('click', function(e) { 

            e.preventDefault();

            e.stopPropagation();

            $('#confirmBackToDocModal').modal('show');

            backto_doc_id = $(this).attr('data-id');

            backto_doc_href = $(this).attr('data-href');

            backto_doc_type = $(this).attr('data-doctype');

        });



        $(document).delegate('#confirmBackToDocModal .accept', 'click', function(){

            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.admin + '/documents/back-to-doc',

                type:'POST',

                datatype:'json',

                data:{doc_id:backto_doc_id, doc_type:backto_doc_type },

                success:function(response){

                    $('.popuploader').hide();

                    var res = JSON.parse(response);

                    $('#confirmBackToDocModal').modal('hide');

                    if(res.status){

                        //if(res.doc_type == 'documents') {

                            $('.notuseddocumnetlist #id_'+res.doc_id).remove();

                        //}

                        localStorage.setItem('activeTab', 'documentalls');

                        location.reload();

                        /*if(res.docInfo) {

                            var subArray = res.docInfo;

                            var trRow = "";

                            trRow += "<tr class='drow' id='id_"+subArray.id+"'><td>"+subArray.checklist+"</td><td>"+ res.Added_By + "<br>" + res.Added_date+"</td><td><i class='fas fa-file-image'></i> <span>"+subArray.file_name+'.'+subArray.filetype+"</span></div></td><td>"+res.Verified_By+ "<br>" +res.Verified_At+"</td></tr>";

                            $('.notuseddocumnetlist').append(trRow);

                        }*/

                        getallactivities();

                    }

                }

            });

        });





        var notid = '';

        var delhref = '';

        $('.deletenote').off('click').on('click', function(e) { 

            e.preventDefault();

            e.stopPropagation();

            $('#confirmModal').modal('show');

            notid = $(this).attr('data-id');

            delhref = $(this).attr('data-href');

           

        });



        $(document).on('click', '.deletenote', function(e) {

            e.preventDefault();

            e.stopPropagation();

            $('#confirmModal').modal('show');

            notid = $(this).attr('data-id');

            delhref = $(this).attr('data-href');

            

        });



        // Cost Agreement Deletion Handler

        var costAgreementId = '';

        $('.deleteCostAgreement').off('click').on('click', function(e) { 

            e.preventDefault();

            e.stopPropagation();

            $('#confirmCostAgreementModal').modal('show');

            costAgreementId = $(this).attr('data-id');

        });



        $(document).on('click', '.deleteCostAgreement', function(e) {

            e.preventDefault();

            e.stopPropagation();

            $('#confirmCostAgreementModal').modal('show');

            costAgreementId = $(this).attr('data-id');

        });



        // Cost Agreement Deletion Confirmation Handler

        $(document).delegate('#confirmCostAgreementModal .acceptCostAgreementDelete', 'click', function(){

            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.deleteCostagreement,

                type:'GET',

                datatype:'json',

                data:{cost_agreement_id:costAgreementId},

                success:function(response){

                    $('.popuploader').hide();

                    var res = JSON.parse(response);

                    $('#confirmCostAgreementModal').modal('hide');

                    if(res.status){

                        // Remove the table row from the DOM

                        $('button[data-id="'+costAgreementId+'"]').closest('tr').remove();

                        

                        // Check if there are any remaining rows, if not show empty message

                        if($('.costform-table tbody tr').length === 0){

                            $('.costform-table').closest('.bg-white').html('<p class="text-gray-600 text-center py-6">No Cost Assignment records found for this client.</p>');

                        }

                        

                        // Show success message

                        alert('Cost Agreement deleted successfully!');

                    } else {

                        alert('Error: ' + (res.message || 'Failed to delete Cost Agreement'));

                    }

                },

                error: function(xhr, status, error) {

                    $('.popuploader').hide();

                    $('#confirmCostAgreementModal').modal('hide');

                    alert('Error: Failed to delete Cost Agreement. Please try again.');

                }

            });

        });



        $(document).delegate('#confirmModal .accept', 'click', function(){

            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.admin + '/documents/delete',

                type:'GET',

                datatype:'json',

                data:{note_id:notid},

                success:function(response){

                    $('.popuploader').hide();

                    var res = JSON.parse(response);

                    $('#confirmModal').modal('hide');

                    if(res.status){

                        $('#note_id_'+notid).remove();

                        if(res.status == true){

                            $('#id_'+notid).remove();

                        }



                        if(delhref == 'deletedocs'){

                            $('.documnetlist_'+res.doc_categry+' #id_'+notid).remove();

                        }

                        if(delhref == 'deleteservices'){

                            $.ajax({

                                url: site_url+'/get-services',

                                type:'GET',

                                data:{clientid:window.ClientDetailConfig.clientId},

                                success: function(responses){

                                    $('.interest_serv_list').html(responses);

                                }

                            });

                        }else if(delhref == 'superagent'){

                            $('.supagent_data').html('');

                        }else if(delhref == 'subagent'){

                            $('.subagent_data').html('');

                        }else if(delhref == 'deleteappointment'){

                            $.ajax({

                                url: site_url+'/get-appointments',

                                type:'GET',

                                data:{clientid:window.ClientDetailConfig.clientId},

                                success: function(responses){

                                    $('.appointmentlist').html(responses);

                                }

                            });

                        } else if(delhref == 'deletepaymentschedule'){

                            $.ajax({

                                url: site_url+'/get-all-paymentschedules',

                                type:'GET',

                                data:{client_id:window.ClientDetailConfig.clientId,appid:res.application_id},

                                success: function(responses){

                                    $('.showpaymentscheduledata').html(responses);

                                }

                            });

                        } else if(delhref == 'deleteapplicationdocs'){

                            $('.mychecklistdocdata').html(res.doclistdata);

                            $('.checklistuploadcount').html(res.applicationuploadcount);

                            $('.'+res.type+'_checklists').html(res.checklistdata);

                        } else {

                            getallnotes();

                            

                        }

                        getallactivities();

                    }

                }

            });

        });







        var activitylogid = '';

        var delloghref = '';

        $(document).delegate('.deleteactivitylog', 'click', function(){

            $('#confirmLogModal').modal('show');

            activitylogid = $(this).attr('data-id');

            delloghref = $(this).attr('data-href');

        });



        $(document).delegate('#confirmLogModal .accept', 'click', function(){

            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.admin + '/' + delloghref,

                type:'GET',

                datatype:'json',

                data:{activitylogid:activitylogid},

                success:function(response){

                    //$('.popuploader').hide();

                    var res = JSON.parse(response);

                    $('#confirmLogModal').modal('hide');

                    //location.reload();

                    if(res.status){

                        $('#activity_'+activitylogid).remove();

                        if(res.status == true){

                            $('#activity_'+activitylogid).remove();

                        }

                        getallactivities();

                    }

                }

            });

        });





        $(document).on('click', '.pinnote', function(e) {

            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.pinNote + '/',

                type:'GET',

                datatype:'json',

                data:{note_id:$(this).attr('data-id')},

                success:function(response){

                    getallnotes();

                }

            });

        });



        $('.pinnote').off('click').on('click', function(e) { 

            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.pinNote + '/',

                type:'GET',

                datatype:'json',

                data:{note_id:$(this).attr('data-id')},

                success:function(response){

                    getallnotes();

                }

            });

        });



        //Pin activity log click

        $(document).delegate('.pinactivitylog', 'click', function(){

            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.pinActivityLog + '/',

                type:'GET',

                datatype:'json',

                data:{activity_id:$(this).attr('data-id')},

                success:function(response){

                    getallactivities();

                }

            });

        });



        $(document).delegate('.createapplicationnewinvoice', 'click', function(){

            $('#opencreateinvoiceform').modal('show');

            var sid	= $(this).attr('data-id');

            var cid	= $(this).attr('data-cid');

            var aid	= $(this).attr('data-app-id');

            $('#client_id').val(cid);

            $('#app_id').val(aid);

            $('#schedule_id').val(sid);

        });



        $(document).delegate('.create_note_d', 'click', function(){



            $('#create_note_d').modal('show');

            $('#create_note_d input[name="mailid"]').val(0);





            $('#create_note_d input[name="title"]').val("Matter Discussion");



            //$('#create_note input[name="title"]').val('');

            $('#create_note_d #appliationModalLabel').html('Create Note');

            // alert('yes');

            //	$('#create_note input[name="title"]').val('');

            //	$("#create_note .summernote-simple").val('');

            //	$('#create_note input[name="noteid"]').val('');

            //	$("#create_note .summernote-simple").summernote('code','');



            if($(this).attr('datatype') == 'note'){

                $('.is_not_note').hide();

            }else{

                var datasubject = $(this).attr('datasubject');

                var datamailid = $(this).attr('datamailid');

                $('#create_note_d input[name="title"]').val(datasubject);

                $('#create_note_d input[name="mailid"]').val(datamailid);

                $('.is_not_note').show();

            }

        });



        $(document).delegate('.create_note', 'click', function(){

            $('#create_note').modal('show');

            $('#create_note input[name="mailid"]').val(0);

            $('#create_note input[name="title"]').val('');

            $('#create_note #appliationModalLabel').html('Create Note');

            $('#create_note input[name="title"]').val('');

            $("#create_note .summernote-simple").val('');

            $('#create_note input[name="noteid"]').val('');

            $("#create_note .summernote-simple").summernote('code','');

            if($(this).attr('datatype') == 'note'){

                $('.is_not_note').hide();

            }else{

                var datasubject = $(this).attr('datasubject');

                var datamailid = $(this).attr('datamailid');

                $('#create_note input[name="title"]').val(datasubject);

                $('#create_note input[name="mailid"]').val(datamailid);

                $('.is_not_note').show();

            }

        });



        $(document).delegate('.opentaskmodal', 'click', function(){

            $('#opentaskmodal').modal('show');

            $('#opentaskmodal input[name="mailid"]').val(0);

            $('#opentaskmodal input[name="title"]').val('');

            $('#opentaskmodal #appliationModalLabel').html('Create Note');

            $('#opentaskmodal input[name="attachments"]').val('');

            $('#opentaskmodal input[name="title"]').val('');

            $('#opentaskmodal .showattachment').val('Choose file');



            var datasubject = $(this).attr('datasubject');

            var datamailid = $(this).attr('datamailid');

            $('#opentaskmodal input[name="title"]').val(datasubject);

            $('#opentaskmodal input[name="mailid"]').val(datamailid);

        });



        $('.js-data-example-ajaxcc').select2({

            multiple: true,

            closeOnSelect: false,

            dropdownParent: $('#create_note'),

            ajax: {

                url: window.ClientDetailConfig.urls.getRecipients,

                dataType: 'json',

                processResults: function (data) {

                // Transforms the top-level key of the response object from 'items' to 'results'

                return {

                    results: data.items

                };



                },

                cache: true



            },

            templateResult: formatRepo,

            templateSelection: formatRepoSelection

        });



        $('.js-data-example-ajaxccapp').select2({

                multiple: true,

                closeOnSelect: false,

                dropdownParent: $('#applicationemailmodal'),

                ajax: {

                    url: window.ClientDetailConfig.urls.getRecipients,

                    dataType: 'json',

                    processResults: function (data) {

                    // Transforms the top-level key of the response object from 'items' to 'results'

                    return {

                        results: data.items

                    };



                    },

                    cache: true



                },

            templateResult: formatRepo,

            templateSelection: formatRepoSelection

        });



        $('.js-data-example-ajaxcontact').select2({

                multiple: true,

                closeOnSelect: false,

                dropdownParent: $('#opentaskmodal'),

                ajax: {

                    url: window.ClientDetailConfig.urls.getRecipients,

                    dataType: 'json',

                    processResults: function (data) {

                    // Transforms the top-level key of the response object from 'items' to 'results'

                    return {

                        results: data.items

                    };



                    },

                    cache: true



                },

            templateResult: formatRepo,

            templateSelection: formatRepoSelection

        });



        function formatRepo (repo) {

        if (repo.loading) {

            return repo.text;

        }



        var $container = $(

            "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +



            "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +

                "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +



            "</div>" +

            "</div>" +

            "<div class='ag-flex ag-flex-column ag-align-end'>" +



                "<span class='ui label yellow select2-result-repository__statistics'>" +



                "</span>" +

            "</div>" +

            "</div>"

        );



        $container.find(".select2-result-repository__title").text(repo.name);

        $container.find(".select2-result-repository__description").text(repo.email);

        $container.find(".select2-result-repository__statistics").append(repo.status);



        return $container;

        }



        //Function is used for complete the session

        $(document).delegate('.complete_session', 'click', function(){

            var client_id = $(this).attr('data-clientid'); //alert(client_id);

            if(client_id !=""){

                $.ajax({

                    type:'post',

                    url: window.ClientDetailConfig.urls.updateSessionCompleted,

                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

                    data: {client_id:client_id },

                    success: function(response){

                        var obj = $.parseJSON(response);

                        location.reload();

                    }

                });

            }

        });



        function formatRepoSelection (repo) {

            return repo.name || repo.text;

        }



        //Update note - Single event handler for both page load and dynamically added elements

        $(document).on('click', '.opennoteform', function(e){

            e.preventDefault();

           

            

            // Check if modal exists

            if($('#create_note').length === 0) {

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

                type:'GET',

                datatype:'json',

                data:{note_id:v},

                success:function(response){

                    $('.popuploader').hide();

                    var res = JSON.parse(response);

                    if(res.status){

                        $('#create_note select[name="task_group"]').val(res.data.task_group);

                        $("#create_note .summernote-simple").val(res.data.description);

                        $("#create_note .summernote-simple").summernote('code',res.data.description);

                    } else {

                        console.error('Note details not found or error in response');

                    }

                },

                error: function(xhr, status, error) {

                    $('.popuploader').hide();

                    console.error('Error fetching note details:', error);

                    console.error('XHR Status:', xhr.status);

                    console.error('Response Text:', xhr.responseText);

                }

            });

        });



        //Edit Notes dynamic time

        $('.opennoteform').off('click').on('click', function(e) { 

            // Check if modal exists

            if($('#create_note').length === 0) {

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

                type:'GET',

                datatype:'json',

                data:{note_id:v},

                success:function(response){

                    $('.popuploader').hide();

                    var res = JSON.parse(response);

                    if(res.status){

                        $('#create_note select[name="task_group"]').val(res.data.task_group);

                        $("#create_note .summernote-simple").val(res.data.description);

                        $("#create_note .summernote-simple").summernote('code',res.data.description);

                    } else {

                        console.error('Note details not found or error in response');

                    }

                },

                error: function(xhr, status, error) {

                    $('.popuploader').hide();

                    console.error('Error fetching note details:', error);

                    console.error('XHR Status:', xhr.status);

                    console.error('Response Text:', xhr.responseText);

                }

            });

        });



        $(document).delegate('.viewnote', 'click', function(){

            $('#view_note').modal('show');

            var v = $(this).attr('data-id');

            $('#view_note input[name="noteid"]').val(v);

                $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.viewNoteDetail,

                type:'GET',

                datatype:'json',

                data:{note_id:v},

                success:function(response){

                    $('.popuploader').hide();

                    var res = JSON.parse(response);



                    if(res.status){

                        $('#view_note .modal-body .note_content h5').html(res.data.title);

                        $("#view_note .modal-body .note_content p").html(res.data.description);



                    }

                }

            });

        });



        $(document).delegate('.viewapplicationnote', 'click', function(){

            $('#view_application_note').modal('show');

            var v = $(this).attr('data-id');

            $('#view_application_note input[name="noteid"]').val(v);

                $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.viewApplicationNote,

                type:'GET',

                datatype:'json',

                data:{note_id:v},

                success:function(response){

                    $('.popuploader').hide();

                    var res = JSON.parse(response);



                    if(res.status){

                        $('#view_application_note .modal-body .note_content h5').html(res.data.title);

                        $("#view_application_note .modal-body .note_content p").html(res.data.description);



                    }

                }

            });

        });



	    $(document).delegate('.add_appliation #workflow', 'change', function(){

            var v = $('.add_appliation #workflow option:selected').val();

			if(v != ''){

				$('.popuploader').show();

                $.ajax({

                    url: window.ClientDetailConfig.urls.getPartnerBranch,

                    type:'GET',

                    data:{cat_id:v},

                    success:function(response){

                        $('.popuploader').hide();

                        $('.add_appliation #partner').html(response);



                        $(".add_appliation #partner").val('').trigger('change');

                        $(".add_appliation #product").val('').trigger('change');

                        $(".add_appliation #branch").val('').trigger('change');

                    }

                });

			}

	    });



        $(document).delegate('.clientemail', 'click', function(){

            if ($('.general_matter_checkbox_client_detail').is(':checked')) {

                var selectedMatterL = $('.general_matter_checkbox_client_detail').val();

            } else {

                var selectedMatterL = $('#sel_matter_id_client_detail').val();

            }

            $('#emailmodal #compose_client_matter_id').val(selectedMatterL);

            $('#emailmodal').modal('show');

            var array = [];

            var data = [];



            var id = $(this).attr('data-id');

            array.push(id);

            var email = $(this).attr('data-email');

            var name = $(this).attr('data-name');

            var status = 'Client';



            data.push({

                id: id,

                text: name,

                html:  "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +



                "<div  class='ag-flex ag-align-start'>" +

                    "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'>"+name+"</span>&nbsp;</div>" +

                    "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>"+email+"</small ></div>" +



                "</div>" +

                "</div>" +

                "<div class='ag-flex ag-flex-column ag-align-end'>" +



                    "<span class='ui label yellow select2-result-repository__statistics'>"+ status +



                    "</span>" +

                "</div>" +

                "</div>",

                title: name

            });



            $(".js-data-example-ajax").select2({

                data: data,

                escapeMarkup: function(markup) {

                    return markup;

                },

                templateResult: function(data) {

                    return data.html;

                },

                templateSelection: function(data) {

                    return data.text;

                }

            })



            $('.js-data-example-ajax').val(array);

            $('.js-data-example-ajax').trigger('change');

        });



        $(document).delegate('.change_client_status', 'click', function(e){



            var v = $(this).attr('rating');

            $('.change_client_status').removeClass('active');

            $(this).addClass('active');



            $.ajax({

                url: window.ClientDetailConfig.urls.changeClientStatus,

                type:'GET',

                datatype:'json',

                data:{id:window.ClientDetailConfig.clientId,rating:v},

                success: function(response){

                    var res = JSON.parse(response);

                    if(res.status){



                        $('.custom-error-msg').html('<span class="alert alert-success">'+res.message+'</span>');

                        getallactivities();

                    }else{

                        $('.custom-error-msg').html('<span class="alert alert-danger">'+response.message+'</span>');

                    }



                }

            });

        });



        /*$(document).delegate('.selecttemplate', 'change', function(){

            var v = $(this).val();

            $.ajax({

                url: window.ClientDetailConfig.urls.getTemplates,

                type:'GET',

                datatype:'json',

                data:{id:v},

                success: function(response){

                    var res = JSON.parse(response);

                    $('.selectedsubject').val(res.subject);

                    $("#emailmodal .summernote-simple").summernote('reset');

                    $("#emailmodal .summernote-simple").summernote('code', res.description);

                    $("#emailmodal .summernote-simple").val(res.description);

                }

            });

        });*/



        $(document).delegate('.selecttemplate', 'change', function(){

            var client_id = $(this).data('clientid'); //alert(client_id);

            var client_firstname = $(this).data('clientfirstname'); //alert(client_firstname);

            if (client_firstname) {

                client_firstname = client_firstname.charAt(0).toUpperCase() + client_firstname.slice(1);

            }

            var client_reference_number = $(this).data('clientreference_number'); //alert(client_reference_number);

            var company_name = 'Bansal Education Group';

            var visa_valid_upto = $(this).data('clientvisaExpiry');

            if ( visa_valid_upto != '' && visa_valid_upto != '0000-00-00') {

                visa_valid_upto = visa_valid_upto;

            } else {

                visa_valid_upto = '';

            }



            var clientassignee_name = $(this).data('clientassignee_name');

            if ( clientassignee_name != '') {

                clientassignee_name = clientassignee_name;

            } else {

                clientassignee_name = '';

            }



            var v = $(this).val();

            $.ajax({

                url: window.ClientDetailConfig.urls.getTemplates,

                type:'GET',

                datatype:'json',

                data:{id:v},

                success: function(response){

                    var res = JSON.parse(response);



                    // Replace {Client First Name} with actual client name

                    //var subjct_message = res.subject

                    //.replace('{Client First Name}', client_firstname)

                    //.replace(/Ref:\s*\.{1,}\s*/, 'Ref: ' + client_reference_number)

                    //.replace(/Ref_\s*-{1,}\s*/, 'Ref_' + client_reference_number)

                    //.replace('{client reference}', client_reference_number);



                    var subjct_message = res.subject.replace('{Client First Name}', client_firstname).replace('{client reference}', client_reference_number);

                    $('.selectedsubject').val(subjct_message);



                    $("#emailmodal .summernote-simple").summernote('reset');

                    //$("#emailmodal .summernote-simple").summernote('code', res.description);

                    //$("#emailmodal .summernote-simple").val(res.description);

                    //var subjct_description = res.description.replace('{Client First Name}', client_firstname);



                    //var subjct_description = res.description

                    //.replace(/Dear\s*\.{2,}\s*/, 'Dear ' + client_firstname)

                    //.replace('{Client First Name}', client_firstname)

                // .replace('{Company Name}', company_name)

                    //.replace('{Visa Valid Upto}', visa_valid_upto)

                    //.replace('{Client Assignee Name}', clientassignee_name)

                    //.replace(/Reference:\s*\.{2,}\s*/, 'Reference: ' + client_reference_number)

                    //.replace('{client reference}', client_reference_number);



                    var subjct_description = res.description

                    .replace('{Client First Name}', client_firstname)

                    .replace('{Company Name}', company_name)

                    .replace('{Visa Valid Upto}', visa_valid_upto)

                    .replace('{Client Assignee Name}', clientassignee_name)

                    .replace('{client reference}', client_reference_number);



                    $("#emailmodal .summernote-simple").summernote('code', subjct_description);

                    $("#emailmodal .summernote-simple").val(subjct_description);

                }

            });

        });



        $(document).delegate('.selectapplicationtemplate', 'change', function(){

            var v = $(this).val();

            $.ajax({

                url: window.ClientDetailConfig.urls.getTemplates,

                type:'GET',

                datatype:'json',

                data:{id:v},

                success: function(response){

                    var res = JSON.parse(response);

                    $('.selectedappsubject').val(res.subject);

                    $("#applicationemailmodal .summernote-simple").summernote('reset');

                    $("#applicationemailmodal .summernote-simple").summernote('code', res.description);

                    $("#applicationemailmodal .summernote-simple").val(res.description);

                }

            });

        });



        $('.js-data-example-ajax').select2({

                multiple: true,

                closeOnSelect: false,

                dropdownParent: $('#emailmodal'),

                ajax: {

                    url: window.ClientDetailConfig.urls.getRecipients,

                    dataType: 'json',

                    processResults: function (data) {

                    // Transforms the top-level key of the response object from 'items' to 'results'

                    return {

                        results: data.items

                    };



                    },

                    cache: true



                },

            templateResult: formatRepo,

            templateSelection: formatRepoSelection

        });



        $('.js-data-example-ajaxccd').select2({

            multiple: true,

            closeOnSelect: false,

            dropdownParent: $('#emailmodal'),

            ajax: {

                url: window.ClientDetailConfig.urls.getRecipients,

                dataType: 'json',

                processResults: function (data) {

                    // Transforms the top-level key of the response object from 'items' to 'results'

                    return {

                        results: data.items

                    };

                },

                cache: true

            },

            templateResult: formatRepo,

            templateSelection: formatRepoSelection

        });



        function formatRepo (repo) {

            if (repo.loading) {

                return repo.text;

            }



            var $container = $(

                "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +



                    "<div  class='ag-flex ag-align-start'>" +

                    "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +

                    "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +



                    "</div>" +

                    "</div>" +

                    "<div class='ag-flex ag-flex-column ag-align-end'>" +



                    "<span class='ui label yellow select2-result-repository__statistics'>" +



                    "</span>" +

                    "</div>" +

                "</div>"

                );



            $container.find(".select2-result-repository__title").text(repo.name);

            $container.find(".select2-result-repository__description").text(repo.email);

            $container.find(".select2-result-repository__statistics").append(repo.status);

            return $container;

        }



        function formatRepoSelection (repo) {

            return repo.name || repo.text;

        }



        /* $(".table-2").dataTable({

            "searching": false,

            "lengthChange": false,

        "columnDefs": [

            { "sortable": false, "targets": [0, 2, 3] }

        ],

        order: [[1, "desc"]] //column indexes is zero based



        }); */

        $('#mychecklist-datatable').dataTable({"searching": true,});

        $(".invoicetable").dataTable({

            "searching": false,

            "lengthChange": false,

            "columnDefs": [

            { "sortable": false, "targets": [0, 2, 3] }

        ],

        order: [[1, "desc"]] //column indexes is zero based



        });





        $(document).delegate('#intrested_workflow', 'change', function(){



			var v = $('#intrested_workflow option:selected').val();

            if(v != ''){

                $('.popuploader').show();

                $.ajax({

                    url: window.ClientDetailConfig.urls.getPartner,

                    type:'GET',

                    data:{cat_id:v},

                    success:function(response){

                        $('.popuploader').hide();

                        $('#intrested_partner').html(response);



                        $("#intrested_partner").val('').trigger('change');

                    $("#intrested_product").val('').trigger('change');

                    $("#intrested_branch").val('').trigger('change');

                    }

                });

            }

	    });



        $(document).delegate('#edit_intrested_workflow', 'change', function(){



                    var v = $('#edit_intrested_workflow option:selected').val();



                    if(v != ''){

                            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.getPartner,

                type:'GET',

                data:{cat_id:v},

                success:function(response){

                    $('.popuploader').hide();

                    $('#edit_intrested_partner').html(response);



                    $("#edit_intrested_partner").val('').trigger('change');

                $("#edit_intrested_product").val('').trigger('change');

                $("#edit_intrested_branch").val('').trigger('change');

                }

            });

                    }

        });



        $(document).delegate('#intrested_partner','change', function(){

            var v = $('#intrested_partner option:selected').val();

            if(v != ''){

                $('.popuploader').show();

                $.ajax({

                    url: window.ClientDetailConfig.urls.getProduct,

                    type:'GET',

                    data:{cat_id:v},

                    success:function(response){

                        $('.popuploader').hide();

                        $('#intrested_product').html(response);

                        $("#intrested_product").val('').trigger('change');

                    $("#intrested_branch").val('').trigger('change');

                    }

                });

            }

        });



        $(document).delegate('#edit_intrested_partner','change', function(){

            var v = $('#edit_intrested_partner option:selected').val();

            if(v != ''){

                $('.popuploader').show();

                $.ajax({

                    url: window.ClientDetailConfig.urls.getProduct,

                    type:'GET',

                    data:{cat_id:v},

                    success:function(response){

                        $('.popuploader').hide();

                        $('#edit_intrested_product').html(response);

                        $("#edit_intrested_product").val('').trigger('change');

                    $("#edit_intrested_branch").val('').trigger('change');

                    }

                });

            }

        });



        $(document).delegate('#intrested_product','change', function(){

            var v = $('#intrested_product option:selected').val();

            if(v != ''){

                $('.popuploader').show();

                $.ajax({

                    url: window.ClientDetailConfig.urls.getBranch,

                    type:'GET',

                    data:{cat_id:v},

                    success:function(response){

                        $('.popuploader').hide();

                        $('#intrested_branch').html(response);

                        $("#intrested_branch").val('').trigger('change');

                    }

                });

            }

        });





        



        // Ensure the event listener is attached to all .add-document buttons

        $(document).on('click', '.add-document', function(e) {

            e.preventDefault(); // Prevent default anchor behavior

            var fileid = $(this).data('fileid');

            $('#upload_form_' + fileid).find('.docupload').click();

        });



        $(document).delegate('.docupload', 'change', function () {

            var fileInput = this;

            var file = fileInput.files[0];

            if (!file) return;



            var fileidL = $(this).attr("data-fileid");

            var doccategoryL = $(this).attr("data-doccategory");

            var formData = new FormData($(this).closest('form')[0]);



            var validNameRegex = /^[a-zA-Z0-9_\-\.\s\$]+$/;

            if (!validNameRegex.test(file.name)) {

                alert("File name can only contain letters, numbers, dashes (-), underscores (_), spaces, dots (.), and dollar signs ($). Please rename the file and try again.");

                $(this).val('');

                return false;

            }



            // Show immediate feedback that upload is starting

            $('.custom-error-msg').html('<span class="alert alert-info"><i class="fa fa-clock-o"></i> Uploading document...</span>');



            $.ajax({

                url: site_url + '/documents/upload-edu-document',

                type: 'POST',

                dataType: 'json',

                data: formData,

                contentType: false,

                processData: false,

                success: function (ress) {

                    if (ress.status) {

                        $('.custom-error-msg').html('<span class="alert alert-success">' + ress.message + '</span>');



                        var row = $('#id_' + fileidL);

                        var docNameWithoutExt = ress.filename.replace(/\.[^/.]+$/, "").replace(/\s+/g, "_").toLowerCase();



                        // Replace upload TD content (Column 1 = File Name)

                        var uploadTd = row.find('td').eq(1);

                        uploadTd.html(

                            '<div data-id="' + fileidL + '" data-name="' + docNameWithoutExt + '" class="doc-row" title="Uploaded by: Admin" oncontextmenu="showFileContextMenu(event, ' + fileidL + ', \'' + ress.filetype + '\', \'' + ress.fileurl + '\', \'' + doccategoryL + '\', \'' + (ress.status_value || 'draft') + '\'); return false;">' +

                                '<a href="javascript:void(0);" onclick="previewFile(\'' + ress.filetype + '\', \'' + ress.fileurl + '\', \'preview-container-' + doccategoryL + '\')">' +

                                    '<i class="fas fa-file-image"></i> <span>' + ress.filename + '</span>' +

                                '</a>' +

                            '</div>'

                        );



                        // Add hidden elements for context menu actions (Column 2 = Actions)

                        var actionTd = row.find('td').eq(2);

                        actionTd.html(

                            '<a class="renamechecklist" data-id="' + fileidL + '" href="javascript:;" style="display: none;"></a>' +

                            '<a class="renamedoc" data-id="' + fileidL + '" href="javascript:;" style="display: none;"></a>' +

                            '<a class="download-file" data-filelink="' + ress.fileurl + '" data-filename="' + ress.filekey + '" href="#" style="display: none;"></a>' +

                            '<a class="notuseddoc" data-id="' + fileidL + '" data-doctype="' + ress.doctype + '" data-href="notuseddoc" href="javascript:;" style="display: none;"></a>'

                        );

                        

                        // Ensure the row has the proper class for event delegation

                        row.addClass('drow');

                    } else {

                        $('.custom-error-msg').html('<span class="alert alert-danger">' + ress.message + '</span>');

                    }

                    getallactivities();

                }

            });

            $(this).val(''); // Clear input after upload

        });





        $(document).delegate('.add_education_doc', 'click', function (e) {

            e.preventDefault(); // Prevent default button behavior and page refresh

            $("#doccategory").val($(this).attr('data-categoryid'));

            $("#folder_name").val($(this).attr('data-categoryid'));

            $('.create_education_docs').modal('show');

            $("#checklist").select2({dropdownParent: $(".create_education_docs")});

        });



        //Add Personal Document category

        $(document).delegate('.add_personal_doc_cat', 'click', function (e) {

            e.preventDefault(); // Prevent default button behavior and page refresh

            $('.addpersonaldoccatmodel').modal('show');

        });



        //Add Visa Document category

        $(document).delegate('.add-visa-doc-category', 'click', function (e) {

            e.preventDefault(); // Prevent default button behavior and page refresh

            let selectedMatterFM;



            if ($('.general_matter_checkbox_client_detail').is(':checked')) {

                // If checkbox is checked, get its value

                selectedMatterFM = $('.general_matter_checkbox_client_detail').val();

            } else {

                // If checkbox is not checked, get the value from the dropdown

                selectedMatterFM = $('#sel_matter_id_client_detail').val();

            }

            $('#visaclientmatterid').val(selectedMatterFM);

            $('.addvisadoccatmodel').modal('show');

        });





        $(document).delegate('.add_migration_doc', 'click', function (e) {

            e.preventDefault(); // Prevent default button behavior and page refresh

            var hidden_client_matter_id = $('#sel_matter_id_client_detail').val();

            $('#hidden_client_matter_id').val(hidden_client_matter_id);

            $("#visa_folder_name").val($(this).attr('data-categoryid'));

            $('.create_migration_docs').modal('show');

            $("#visa_checklist").select2({dropdownParent: $("#openmigrationdocsmodal")});

        });



        $(document).delegate('.add_application_btn', 'click', function () {

            var hidden_client_matter_id = $('#sel_matter_id_client_detail').val();

            $('#hidden_client_matter_id_latest').val(hidden_client_matter_id);

        });



        $(document).delegate('.migdocupload', 'click', function() {

            $(this).attr("value", "");

        });



        





        $(document).delegate('.migdocupload', 'change', function() {

            var fileInput = this.files[0];



            if (!fileInput) return; // Prevent empty uploads



            var fileName = fileInput.name;  //alert(fileName);



            // Allowed: letters, numbers, dash, underscore, space, dot, dollar sign

            var validNameRegex = /^[a-zA-Z0-9_\-\.\s\$]+$/;



            if (!validNameRegex.test(fileName)) {

                alert("File name can only contain letters, numbers, dashes (-), underscores (_), spaces, dots (.), and dollar signs ($). Please rename the file and try again.");

                $(this).val(''); // Clear the file input

                return false;

            }



            var fileidL1 = $(this).attr("data-fileid");

           



            var visa_doc_cat = $(this).attr("data-doccategory");

            



            // Show immediate feedback that upload is starting

            $('.custom-error-msg').html('<span class="alert alert-info"><i class="fa fa-clock-o"></i> Uploading document...</span>');

            

            // Create FormData before clearing the input

            var formData = new FormData($('#mig_upload_form_'+fileidL1)[0]);

            // Append extra data manually

            formData.append('visa_doc_cat', visa_doc_cat);

            

            // Clear the file input after creating FormData to allow next upload

            $(this).val('');

            

            $.ajax({

                url: site_url+'/documents/upload-visa-document',

                type:'POST',

                dataType: 'json',

                data: formData,

                contentType: false,

                processData: false,

                success: function(ress) {

                    if (ress.status) {

                        $('.custom-error-msg').html('<span class="alert alert-success">' + ress.message + '</span>');



                        var row = $('#id_' + fileidL1);

                        var docNameWithoutExt = ress.filename.replace(/\.[^/.]+$/, "").replace(/\s+/g, "_").toLowerCase();



                        // Replace upload TD content (Column 1 = File Name)

                        var uploadTd = row.find('td').eq(1);

                        uploadTd.html(

                            '<div data-id="' + fileidL1 + '" data-name="' + docNameWithoutExt + '" class="doc-row" title="Uploaded by: Admin" oncontextmenu="showVisaFileContextMenu(event, ' + fileidL1 + ', \'' + ress.filetype + '\', \'' + ress.fileurl + '\', \'' + visa_doc_cat + '\', \'' + (ress.status_value || 'draft') + '\'); return false;">' +

                                '<a href="javascript:void(0);" onclick="previewFile(\'' + ress.filetype + '\', \'' + ress.fileurl + '\', \'preview-container-migdocumnetlist\')">' +

                                    '<i class="fas fa-file-image"></i> <span>' + ress.filename + '</span>' +

                                '</a>' +

                            '</div>'

                        );



                        // Add hidden elements for context menu actions (Column 2 = Actions)

                        var actionTd = row.find('td').eq(2);

                        actionTd.html(

                            '<a class="renamechecklist" data-id="' + fileidL1 + '" href="javascript:;" style="display: none;"></a>' +

                            '<a class="renamedoc" data-id="' + fileidL1 + '" href="javascript:;" style="display: none;"></a>' +

                            '<a class="download-file" data-filelink="' + ress.fileurl + '" data-filename="' + ress.filekey + '" href="#" style="display: none;"></a>' +

                            '<a class="notuseddoc" data-id="' + fileidL1 + '" data-doctype="visa" data-href="notuseddoc" href="javascript:;" style="display: none;"></a>'

                        );

                        

                        // Ensure the row has the proper class for event delegation

                        row.addClass('drow');

                    } else {

                        $('.custom-error-msg').html('<span class="alert alert-danger">' + ress.message + '</span>');

                    }

                    getallactivities();

                },

                error: function(xhr, status, error) {

                    $('.custom-error-msg').html('<span class="alert alert-danger">Upload failed. Please try again.</span>');

                    console.error('Upload error:', error);

                    getallactivities();

                }

            });

        });





        $(document).delegate('.converttoapplication','click', function(){

            var v = $(this).attr('data-id');

            if(v != ''){

                $('.popuploader').show();

                $.ajax({

                    url: window.ClientDetailConfig.urls.convertApplication,

                    type:'GET',

                    data:{cat_id:v,clientid:window.ClientDetailConfig.clientId},

                    success:function(response){

                        $.ajax({

                            url: site_url+'/get-services',

                            type:'GET',

                            data:{clientid:window.ClientDetailConfig.clientId},

                            success: function(responses){



                                $('.interest_serv_list').html(responses);

                            }

                        });

                        $.ajax({

                            url: site_url+'/get-application-lists',

                            type:'GET',

                            datatype:'json',

                            data:{id:window.ClientDetailConfig.clientId},

                            success: function(responses){

                                $('.applicationtdata').html(responses);

                            }

                        });

                        //getallactivities();

                        $('.popuploader').hide();

                    }

                });

            }

        });



        $(document).on('click', '#application-tab', function () { 

            $('.popuploader').show();

            /*$.ajax({

                url: site_url+'/get-application-lists',

                type:'GET',

                datatype:'json',

                data:{id:window.ClientDetailConfig.clientId},

                success: function(responses){

                    $('.popuploader').hide();

                    $('.applicationtdata').html(responses);

                }

            });*/

        });



        // Initialize event handlers for rename functionality

        $(document).ready(function() {

            

            

            // Test if renamechecklist elements exist

            var renameElements = $('.renamechecklist');

            

            

            // Test if personalchecklist-row elements exist

            var personalRows = $('.personalchecklist-row');

           

        });



        //Rename File Name Personal Document

        $(document).on('click', '.persdocumnetlist .renamedoc', function (e) {

            e.preventDefault();

            e.stopPropagation();

            

           

            

            var parent = $(this).closest('.drow').find('.doc-row');

           

            

            if (parent.length === 0) {

                console.error('Document row not found');

                return false;

            }

            

            parent.data('current-html', parent.html());

            var opentime = parent.data('name');

            

            

            if (!opentime) {

                console.error('Document name not found');

                return false;

            }

            

            parent.empty().append(

                $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),

                $('<button class="btn btn-primary btn-sm mb-1"><i class="fas fa-check"></i></button>'),

                $('<button class="btn btn-danger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')

            );

            

            

            return false;

        });



        $(document).on('click', '.persdocumnetlist .btn-danger', function (e) {

            e.preventDefault();

            e.stopPropagation();

            

          

            

            var parent = $(this).closest('.drow').find('.doc-row');

            if (parent.length === 0) {

                console.error('Document row not found for cancel');

                return false;

            }

            

            var hourid = parent.data('id');

            if (hourid) {

                parent.html(parent.data('current-html'));

            } else {

                parent.remove();

            }

            return false;

        });



        $(document).on('click', '.persdocumnetlist .btn-primary', function (e) {

            e.preventDefault();

            e.stopPropagation();

            

            

            

            var parent = $(this).closest('.drow').find('.doc-row');

            if (parent.length === 0) {

                console.error('Document row not found for save');

                return false;

            }

            

            parent.find('.opentime').removeClass('is-invalid');

            parent.find('.invalid-feedback').remove();

            var opentime = parent.find('.opentime').val();

            

            if (!opentime) {

                parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });

                parent.append($("<div class='invalid-feedback'>This field is required</div>"));

                return false;

            }

            

           

            

            $.ajax({

                type: "POST",

                data: {"_token": $('meta[name="csrf-token"]').attr('content'),"filename": opentime, "id": parent.data('id')},

                url: window.ClientDetailConfig.urls.renameDoc,

                success: function(result){

                    var obj = JSON.parse(result);

                    if (obj.status) {

                        var previewUrl = obj.fileurl;

                        var filetype = obj.filetype;

                        var folderName = obj.folder_name;

                        var fileName = obj.filename + '.' + obj.filetype;



                        parent.empty()

                            .data('id', obj.Id)

                            .data('name', opentime)

                            .append(

                                $('<a>', {

                                    href: 'javascript:void(0);',

                                    onclick: `previewFile('${filetype}', '${previewUrl}', '${folderName}')`

                                }).append(

                                    $('<i>', { class: 'fas fa-file-image' }),

                                    ' ',

                                    $('<span>').text(fileName)

                                )

                            );

                        

                        if ($('#grid_'+obj.Id).length) {

                            $('#grid_'+obj.Id).html(fileName);

                        }



                        // ðŸ” Update the Preview & Download links in dropdown

                        var dropdownMenu = $(parent).closest('.drow').find('.dropdown-menu');



                        // Preview link

                        dropdownMenu.find('.dropdown-item[href^="http"]').filter(function() {

                            return $(this).text().trim() === 'Preview';

                        }).attr('href', previewUrl);



                        // Download link

                        dropdownMenu.find('.dropdown-item.download-file')

                            .attr('data-filelink', previewUrl)

                            .attr('data-filename', fileName);

                            

                        

                    } else {

                        parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });

                        parent.append($('<div class="invalid-feedback">' + obj.message + '</div>'));

                        console.error('Failed to rename document:', obj.message);

                    }

                },

                error: function(xhr, status, error) {

                    console.error('Ajax error:', error);

                    parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });

                    parent.append($('<div class="invalid-feedback">An error occurred while saving</div>'));

                }

            });

            return false;

        });





        //Rename Checklist Name Personal Document

        $(document).on('click', '.persdocumnetlist .renamechecklist', function (e) {

            e.preventDefault();

            e.stopPropagation();

            

           

            

            var parent = $(this).closest('.drow').find('.personalchecklist-row');

            

            

            if (parent.length === 0) {

                console.error('Personal checklist row not found');

                return false;

            }

            

            parent.data('current-html', parent.html());

            var opentime = parent.data('personalchecklistname');

            

            

            if (!opentime) {

                console.error('Personal checklist name not found');

                return false;

            }

            

            parent.empty().append(

                $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),

                $('<button class="btn btn-personalprimary btn-sm mb-1"><i class="fas fa-check"></i></button>'),

                $('<button class="btn btn-personaldanger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')

            );

            

            

            return false;

        });



        // Alternative event handler for better compatibility

        $(document).on('click', '.persdocumnetlist a.renamechecklist', function (e) {

            e.preventDefault();

            e.stopPropagation();

            

            

            

            var $this = $(this);

            var $drow = $this.closest('.drow');

            var $parent = $drow.find('.personalchecklist-row');

            

            if ($parent.length === 0) {

                console.error('Personal checklist row not found in alternative handler');

                return false;

            }

            

            var opentime = $parent.data('personalchecklistname');

            if (!opentime) {

                console.error('Personal checklist name not found in alternative handler');

                return false;

            }

            

            $parent.data('current-html', $parent.html());

            $parent.empty().append(

                $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),

                $('<button class="btn btn-personalprimary btn-sm mb-1"><i class="fas fa-check"></i></button>'),

                $('<button class="btn btn-personaldanger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')

            );

            

           

            return false;

        });



        $(document).on('click', '.persdocumnetlist .btn-personaldanger', function (e) {

            e.preventDefault();

            e.stopPropagation();

            

            var parent = $(this).closest('.drow').find('.personalchecklist-row');

            if (parent.length === 0) {

                console.error('Personal checklist row not found for cancel');

                return false;

            }

            

            var hourid = parent.data('id');

            if (hourid) {

                parent.html(parent.data('current-html'));

            } else {

                parent.remove();

            }

            return false;

        });



        $(document).on('click', '.persdocumnetlist .btn-personalprimary', function (e) {

            e.preventDefault();

            e.stopPropagation();

            

            var parent = $(this).closest('.drow').find('.personalchecklist-row');

            if (parent.length === 0) {

                console.error('Personal checklist row not found for save');

                return false;

            }

            

            parent.find('.opentime').removeClass('is-invalid');

            parent.find('.invalid-feedback').remove();

            var opentime = parent.find('.opentime').val();

            

            if (!opentime) {

                parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });

                parent.append($("<div class='invalid-feedback'>This field is required</div>"));

                return false;

            }

            

            $.ajax({

                type: "POST",

                data: {"_token": $('meta[name="csrf-token"]').attr('content'),"checklist": opentime, "id": parent.data('id')},

                url: window.ClientDetailConfig.urls.renameChecklistDoc,

                success: function(result){

                    var obj = JSON.parse(result);

                    if (obj.status) {

                        parent.empty()

                            .data('id', obj.Id)

                            .data('personalchecklistname', opentime)

                            .append(

                                $('<span>').html(obj.checklist)

                            );

                        if ($('#grid_'+obj.Id).length) {

                            $('#grid_'+obj.Id).html(obj.checklist);

                        }

                    } else {

                        parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });

                        parent.append($('<div class="invalid-feedback">' + obj.message + '</div>'));

                    }

                },

                error: function(xhr, status, error) {

                    console.error('Ajax error:', error);

                    parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });

                    parent.append($('<div class="invalid-feedback">An error occurred while saving</div>'));

                }

            });

            return false;

        });





        //Rename File Name Visa Document

        $(document).on('click', '.migdocumnetlist1 .renamedoc', function (e) {

            e.preventDefault();

            e.stopPropagation();

            

           

            

            var parent = $(this).closest('.drow').find('.doc-row');

           

            

            if (parent.length === 0) {

                console.error('Visa document row not found');

                return false;

            }

            

            parent.data('current-html', parent.html());

            var opentime = parent.data('name');

            

            

            if (!opentime) {

                console.error('Visa document name not found');

                return false;

            }

            

            parent.empty().append(

                $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),

                $('<button class="btn btn-primary btn-sm mb-1"><i class="fas fa-check"></i></button>'),

                $('<button class="btn btn-danger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')

            );

            

            

            return false;

        });





        $(document).on('click', '.migdocumnetlist1 .btn-danger', function (e) {

            e.preventDefault();

            e.stopPropagation();

            

          

            

            var parent = $(this).closest('.drow').find('.doc-row');

            if (parent.length === 0) {

                console.error('Visa document row not found for cancel');

                return false;

            }

            

            var hourid = parent.data('id');

            if (hourid) {

                parent.html(parent.data('current-html'));

            } else {

                parent.remove();

            }

            return false;

        });



        $(document).on('click', '.migdocumnetlist1 .btn-primary', function (e) {

            e.preventDefault();

            e.stopPropagation();

            

           

            

            var parent = $(this).closest('.drow').find('.doc-row');

            if (parent.length === 0) {

                console.error('Visa document row not found for save');

                return false;

            }

            

            parent.find('.opentime').removeClass('is-invalid');

            parent.find('.invalid-feedback').remove();

            var opentime = parent.find('.opentime').val();

            

            if (!opentime) {

                parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });

                parent.append($("<div class='invalid-feedback'>This field is required</div>"));

                return false;

            }

            

           

            

            $.ajax({

                type: "POST",

                data: {"_token": $('meta[name="csrf-token"]').attr('content'),"filename": opentime, "id": parent.data('id')},

                url: window.ClientDetailConfig.urls.renameDoc,

                success: function(result){

                    var obj = JSON.parse(result);

                    if (obj.status) {

                        var previewUrl = obj.fileurl;

                        var filetype = obj.filetype;

                        var folderName = obj.folder_name;

                        var fileName = obj.filename + '.' + obj.filetype;



                        parent.empty()

                            .data('id', obj.Id)

                            .data('name', opentime)

                            .append(

                                $('<a>', {

                                    href: 'javascript:void(0);',

                                    onclick: `previewFile('${filetype}', '${previewUrl}', '${folderName}')`

                                }).append(

                                    $('<i>', { class: 'fas fa-file-image' }),

                                    ' ',

                                    $('<span>').text(fileName)

                                )

                            );



                        if ($('#grid_'+obj.Id).length) {

                            $('#grid_'+obj.Id).html(fileName);

                        }

                        

                        // ðŸ” Update the Preview & Download links in dropdown

                        var dropdownMenu = $(parent).closest('.drow').find('.dropdown-menu');



                        // Preview link

                        dropdownMenu.find('.dropdown-item[href^="http"]').filter(function() {

                            return $(this).text().trim() === 'Preview';

                        }).attr('href', previewUrl);



                        // Download link

                        dropdownMenu.find('.dropdown-item.download-file')

                            .attr('data-filelink', previewUrl)

                            .attr('data-filename', fileName);

                            

                       

                    } else {

                        parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });

                        parent.append($('<div class="invalid-feedback">' + obj.message + '</div>'));

                        console.error('Failed to rename visa document:', obj.message);

                    }

                },

                error: function(xhr, status, error) {

                    console.error('Ajax error:', error);

                    parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });

                    parent.append($('<div class="invalid-feedback">An error occurred while saving</div>'));

                }

            });

            return false;

        });



        //Rename Checklist Name Visa Document

        $(document).on('click', '.migdocumnetlist1 .renamechecklist', function (e) {

            e.preventDefault();

            e.stopPropagation();

            

            

            

            var parent = $(this).closest('.drow').find('.visachecklist-row');

            

            

            if (parent.length === 0) {

                console.error('Visa checklist row not found');

                return false;

            }

            

            parent.data('current-html', parent.html());

            var opentime = parent.data('visachecklistname');

            

            

            if (!opentime) {

                //console.error('Visa checklist name not found');

                return false;

            }

            

            parent.empty().append(

                $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),

                $('<button class="btn btn-visaprimary btn-sm mb-1"><i class="fas fa-check"></i></button>'),

                $('<button class="btn btn-visadanger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')

            );

            

            //('Rename visa checklist interface created successfully');

            return false;

        });





        $(document).on('click', '.migdocumnetlist1 .btn-visadanger', function (e) {

            e.preventDefault();

            e.stopPropagation();

            

            var parent = $(this).closest('.drow').find('.visachecklist-row');

            if (parent.length === 0) {

                //console.error('Visa checklist row not found for cancel');

                return false;

            }

            

            var hourid = parent.data('id');

            if (hourid) {

                parent.html(parent.data('current-html'));

            } else {

                parent.remove();

            }

            return false;

        });



        $(document).on('click', '.migdocumnetlist1 .btn-visaprimary', function (e) {

            e.preventDefault();

            e.stopPropagation();

            

           

            

            var parent = $(this).closest('.drow').find('.visachecklist-row');

            if (parent.length === 0) {

                //console.error('Visa checklist row not found for save');

                return false;

            }

            

            parent.find('.opentime').removeClass('is-invalid');

            parent.find('.invalid-feedback').remove();

            var opentime = parent.find('.opentime').val();

            

            if (!opentime) {

                parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });

                parent.append($("<div class='invalid-feedback'>This field is required</div>"));

                return false;

            }

            

            

            

            $.ajax({

                type: "POST",

                data: {"_token": $('meta[name="csrf-token"]').attr('content'),"checklist": opentime, "id": parent.data('id')},

                url: window.ClientDetailConfig.urls.renameChecklistDoc,

                success: function(result){

                    var obj = JSON.parse(result);

                    if (obj.status) {

                        parent.empty()

                            .data('id', obj.Id)

                            .data('visachecklistname', opentime)

                            .append(

                                $('<span>').html(obj.checklist)

                            );

                        

                        if ($('#grid_'+obj.Id).length) {

                            $('#grid_'+obj.Id).html(obj.checklist);

                        }

                        

                      

                    } else {

                        parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });

                        parent.append($('<div class="invalid-feedback">' + obj.message + '</div>'));

                       console.error('Failed to rename visa checklist:', obj.message);

                    }

                },

                error: function(xhr, status, error) {

                    console.error('Ajax error:', error);

                    parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });

                    parent.append($('<div class="invalid-feedback">An error occurred while saving</div>'));

                }

            });

            return false;

        });







        // Appointment data will be loaded via AJAX or passed from blade template

        // TODO: Move appointment data initialization to blade template

        $(document).delegate('.appointmentdata', 'click', function () {

            var v = $(this).attr('data-id');

            $('.appointmentdata').removeClass('active');

            $(this).addClass('active');

            

            // Check if appointment data exists in window object

            if (typeof window.appointmentData !== 'undefined' && window.appointmentData[v]) {

                var res = window.appointmentData;

                $('.appointmentname').html(res[v].title);

                $('.appointmenttime').html(res[v].time);

                $('.appointmentdate').html(res[v].date);

                $('.appointmentdescription').html(res[v].description);

                $('.appointmentcreatedby').html(res[v].createdby);

                $('.appointmentcreatedname').html(res[v].createdname);

                $('.appointmentcreatedemail').html(res[v].createdemail);

                $('.editappointment .edit_link').attr('data-id', v);

            }

        });



        $(document).delegate('.opencreate_task', 'click', function () {

            $('#tasktermform')[0].reset();

            $('#tasktermform select').val('').trigger('change');

            $('.create_task').modal('show');

            $('.ifselecttask').hide();

            $('.ifselecttask select').attr('data-valid', '');

        });



        var eduid = '';

        $(document).delegate('.deleteeducation', 'click', function(){

            eduid = $(this).attr('data-id');

            $('#confirmEducationModal').modal('show');

        });



        $(document).delegate('#confirmEducationModal .accepteducation', 'click', function(){

            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.deleteEducation,

                type:'GET',

                datatype:'json',

                data:{edu_id:eduid},

                success:function(response){

                    $('.popuploader').hide();

                    var res = JSON.parse(response);

                    $('#confirmEducationModal').modal('hide');

                    if(res.status){

                        $('#edu_id_'+eduid).remove();

                    }else{

                        alert('Please try again')

                    }

                }

            });

        });



        $(document).delegate('#educationform #subjectlist', 'change', function(){

            var v = $('#educationform #subjectlist option:selected').val();

            if(v != ''){

                $('.popuploader').show();

                $.ajax({

                    url: window.ClientDetailConfig.urls.getSubjects,

                    type:'GET',

                    data:{cat_id:v},

                    success:function(response){

                        $('.popuploader').hide();

                        $('#educationform #subject').html(response);

                        $(".add_appliation #subject").val('').trigger('change');

                    }

                });

            }

        });



        $(document).delegate('.edit_appointment', 'click', function(){

            var v = $(this).attr('data-id');

            $('.popuploader').show();

            $('#edit_appointment').modal('show');

            $.ajax({

                url: window.ClientDetailConfig.urls.getAppointmentDetail,

                type:'GET',

                data:{id:v},

                success:function(response){

                    $('.popuploader').hide();

                    $('.showappointmentdetail').html(response);

                    $(".datepicker").daterangepicker({

                        locale: { format: "YYYY-MM-DD" },

                        singleDatePicker: true,

                        showDropdowns: true

                    });

                    $(".timepicker").timepicker({

                            icons: {

                                up: "fas fa-chevron-up",

                                down: "fas fa-chevron-down"

                            }

                    });

                    $(".timezoneselects2").select2({

                        dropdownParent: $("#edit_appointment")

                    });



                    $(".invitesselects2").select2({

                        dropdownParent: $("#edit_appointment")

                    });

                }

            });

        });



        $(".applicationselect2").select2({

            dropdownParent: $(".add_appliation")

        });

        $(".partner_branchselect2").select2({

            dropdownParent: $(".add_appliation")

        });

        $(".approductselect2").select2({

            dropdownParent: $(".add_appliation")

        });

            $(".workflowselect2").select2({

            dropdownParent: $(".add_interested_service")

        });

        $(".partnerselect2").select2({

            dropdownParent: $(".add_interested_service")

        });

        $(".productselect2").select2({

            dropdownParent: $(".add_interested_service")

        });

        $(".branchselect2").select2({

            dropdownParent: $(".add_interested_service")

        });



        $(document).delegate('.editeducation', 'click', function(){

            var v = $(this).attr('data-id');

            $('.popuploader').show();

            $('#edit_education').modal('show');

            $.ajax({

                url: window.ClientDetailConfig.urls.getEducationDetail,

                type:'GET',

                data:{id:v},

                success:function(response){

                    $('.popuploader').hide();

                    $('.showeducationdetail').html(response);

                    $(".datepicker").daterangepicker({

                        locale: { format: "YYYY-MM-DD" },

                        singleDatePicker: true,

                        showDropdowns: true

                    });



                }

            });

        });



        $(document).delegate('.interest_service_view', 'click', function(){

            var v = $(this).attr('data-id');

            $('.popuploader').show();

            $('#interest_service_view').modal('show');

            $.ajax({

                url: window.ClientDetailConfig.urls.getInterestedService,

                type:'GET',

                data:{id:v},

                success:function(response){

                    $('.popuploader').hide();

                    $('.showinterestedservice').html(response);

                }

            });

        });





        $(document).delegate('.openeditservices', 'click', function(){

            var v = $(this).attr('data-id');

            $('.popuploader').show();

            $('#interest_service_view').modal('hide');

            $('#eidt_interested_service').modal('show');

            $.ajax({

                url: window.ClientDetailConfig.urls.getInterestedServiceEdit,

                type:'GET',

                data:{id:v},

                success:function(response){

                    $('.popuploader').hide();

                    $('.showinterestedserviceedit').html(response);

                        $(".workflowselect2").select2({

                        dropdownParent: $("#eidt_interested_service")

                    });



                    $(".partnerselect2").select2({

                        dropdownParent: $("#eidt_interested_service")

                    });



                    $(".productselect2").select2({

                        dropdownParent: $("#eidt_interested_service")

                    });



                    $(".branchselect2").select2({

                        dropdownParent: $("#eidt_interested_service")

                    });

                    $(".datepicker").daterangepicker({

                        locale: { format: "YYYY-MM-DD" },

                        singleDatePicker: true,

                        showDropdowns: true

                    });

                }

            });

        });



        $(document).delegate('.opencommissioninvoice', 'click', function(){

            $('#opencommissionmodal').modal('show');

        });



        $(document).delegate('.opengeneralinvoice', 'click', function(){

            $('#opengeneralinvoice').modal('show');

        });



        //Convert Lead to Client Popup

        $(document).delegate('.convertLeadToClient', 'click', function(){

            $('#convertLeadToClientModal').modal('show');

            $('#sel_migration_agent_id,#sel_person_responsible_id,#sel_person_assisting_id,#sel_matter_id').select2({

                dropdownParent: $('#convertLeadToClientModal')

            });

        });



        //Change matter assignee

        $(document).delegate('.changeMatterAssignee', 'click', function(){

            let selectedMatterLM;



            if ($('.general_matter_checkbox_client_detail').is(':checked')) {

                // If checkbox is checked, get its value

                selectedMatterLM = $('.general_matter_checkbox_client_detail').val();

            } else {

                // If checkbox is not checked, get the value from the dropdown

                selectedMatterLM = $('#sel_matter_id_client_detail').val();

            }



            

            $('#selectedMatterLM').val(selectedMatterLM);



            $.ajax({

                type:'post',

                url: window.ClientDetailConfig.urls.fetchClientMatterAssignee,

                sync:true,

                data: { client_matter_id:selectedMatterLM},

                success: function(response){

                    var obj = $.parseJSON(response);



                    $('#change_sel_migration_agent_id').val(obj.matter_info.sel_migration_agent);

                    $('#change_sel_person_responsible_id').val(obj.matter_info.sel_person_responsible);

                    $('#change_sel_person_assisting_id').val(obj.matter_info.sel_person_assisting);



                    $('#changeMatterAssigneeModal').modal('show');

                    $('#change_sel_migration_agent_id,#change_sel_person_responsible_id,#change_sel_person_assisting_id').select2({

                        dropdownParent: $('#changeMatterAssigneeModal')

                    });

                }

            });

        });



        //Account Tab Receipt Popup

        $(document).delegate('.createreceipt', 'click', function(){

            $('#createreceiptmodal').modal('show');



            // Wait for the modal to be fully shown to check for the visible form

            $('#createreceiptmodal').on('shown.bs.modal', function() {

                // Find the visible form inside the modal

                const activeForm = $(this).find('.form-type:visible');

                // Get the form ID or any attribute you want

                const activeFormId = activeForm.attr('id');

                //var selectedMatter = $('#sel_matter_id_client_detail').val();

                // Get the value based on checkbox state

                let selectedMatter;



                if ($('.general_matter_checkbox_client_detail').is(':checked')) {

                    // If checkbox is checked, get its value

                    selectedMatter = $('.general_matter_checkbox_client_detail').val();

                } else {

                    // If checkbox is not checked, get the value from the dropdown

                    selectedMatter = $('#sel_matter_id_client_detail').val();

                }



                

                // You can also take action based on the form

                if (activeFormId === 'client_receipt_form') {

                    listOfInvoice();

                    clientLedgerBalanceAmount(selectedMatter);

                    $('.report_entry_date_fields').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

                    $('#client_matter_id_ledger').val(selectedMatter);

                }

                else if (activeFormId === 'invoice_receipt_form')

                {

                    $('.report_entry_date_fields_invoice').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

                    $('#client_matter_id_invoice').val(selectedMatter);

                }

                else if (activeFormId === 'office_receipt_form')

                {

                    listOfInvoice();

                    //var recordCnt = isAnyInvoiceNoExistInDB();

                    $('.report_entry_date_fields_office').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

                    $('#client_matter_id_office').val(selectedMatter);

                }

            });

        });



        $('#createreceiptmodal,#createadjustinvoicereceiptmodal').on('show.bs.modal', function() {

            $('.modal-dialog').css('max-width', '85%');

        });



        $('#createReceiptModal').on('hide.bs.modal', function () {

            const activeForm = $(this).find('.form-type:visible');

            const activeFormId = activeForm.attr('id');



            // You can also take action based on the form

            if (activeFormId === 'client_receipt_form') {

                $('#client_receipt_form')[0].reset();

                $('.total_deposit_amount_all_rows').text("");

                //$('#sel_client_agent_id').val("").trigger('change');

                $('.report_entry_date_fields').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

                $('#client_matter_id_ledger').val("");

            }

            else if (activeFormId === 'invoice_receipt_form') {

                $('#function_type').val("");

                $('#invoice_receipt_form')[0].reset();

                $('.total_deposit_amount_all_rows_invoice').text("");

                //$('#sel_invoice_agent_id').val("").trigger('change');

                $('.report_entry_date_fields_invoice').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

                $('#client_matter_id_invoice').val("");

            }

            else if (activeFormId === 'office_receipt_form') {

                $('#office_receipt_form')[0].reset();

                $('.total_withdraw_amount_all_rows_office').text("");

                //$('#sel_office_agent_id').val("").trigger('change');

                $('.report_entry_date_fields_office').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

                $('#client_matter_id_office').val("");

            }

        });



        $.ajaxSetup({

            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            }

        });



        $(document).delegate('.createclientreceipt', 'click', function(){

            getTopReceiptValInDB(1);

            $('#createclientreceiptmodal').modal('show');

        });



        $(document).delegate('.createofficereceipt', 'click', function(){

            getTopReceiptValInDB(2);

            listOfInvoice();

            var recordCnt = isAnyInvoiceNoExistInDB();

        });



        $(document).delegate('.createinvoicereceipt', 'click', function(){

            getTopReceiptValInDB(3);

            $('#function_type').val("add");

            $('#createinvoicereceiptmodal').modal('show');

        });



        $(document).delegate('.createjournalreceipt', 'click', function(){

            getTopReceiptValInDB(4);

            listOfInvoice();

            $('#createjournalreceiptmodal').modal('show');

        });



        $(document).delegate('.updatedraftinvoice', 'click', function(){

            var receiptid = $(this).data('receiptid');

            $('#function_type').val("edit");

           

            getInfoByReceiptId(receiptid);

        });



        //adjust invocie

        $(document).delegate('.adjustinvoice', 'click', function(){

            $('#createadjustinvoicereceiptmodal').modal('show');

            $('#function_type').val("add");

            getTopInvoiceNoFromDB(3);

        });



        $('#createclientreceiptmodal,#createofficereceiptmodal,#createjournalreceiptmodal').on('show.bs.modal', function() {

            $('.modal-dialog').css('max-width', '80%');

        });



        $('#createinvoicereceiptmodal').on('show.bs.modal', function() {

            $('.modal-dialog').css('max-width', '85%');

        });



        //On Close Hide all content from popups

        $('#createclientreceiptmodal').on('hidden.bs.modal', function() {

            $('#create_client_receipt')[0].reset();

            $('.total_deposit_amount_all_rows').text("");

            $('#sel_client_agent_id').val("").trigger('change');

            $('.report_entry_date_fields').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

        });



        $('#createinvoicereceiptmodal').on('hidden.bs.modal', function() {

            $('#function_type').val("");

            $('#create_invoice_receipt')[0].reset();

            $('.total_deposit_amount_all_rows_invoice').text("");

            $('#sel_invoice_agent_id').val("").trigger('change');

            $('.report_entry_date_fields_invoice').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

        });



        $('#createofficereceiptmodal').on('hidden.bs.modal', function() {

            $('#create_office_receipt')[0].reset();

            $('.total_withdraw_amount_all_rows_office').text("");

            $('#sel_office_agent_id').val("").trigger('change');

            $('.report_entry_date_fields_office').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

        });



        $('#createjournalreceiptmodal').on('hidden.bs.modal', function() {

            $('#create_journal_receipt')[0].reset();

            $('.total_withdraw_amount_all_rows_journal').text("");

            $('#sel_journal_agent_id').val("").trigger('change');

            $('.report_entry_date_fields_journal').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

        });





        $(document).delegate('.addpaymentmodal','click', function(){

            var v = $(this).attr('data-invoiceid');

            var netamount = $(this).attr('data-netamount');

            var dueamount = $(this).attr('data-dueamount');

            $('#invoice_id').val(v);

            $('.invoicenetamount').html(netamount+' AUD');

            $('.totldueamount').html(dueamount);

            $('.totldueamount').attr('data-totaldue', dueamount);

            $('#addpaymentmodal').modal('show');

            $('.payment_field_clone').remove();

            $('.paymentAmount').val('');

        });



        $(document).delegate('.paymentAmount','keyup', function(){

		    grandtotal();

        });



		function grandtotal(){

			var p =0;

			$('.paymentAmount').each(function(){

				if($(this).val() != ''){

					p += parseFloat($(this).val());

				}

			});



			var tamount = $('.totldueamount').attr('data-totaldue');

            var am = parseFloat(tamount) - parseFloat(p);

			$('.totldueamount').html(am.toFixed(2));

		}



        $('.add_payment_field a').on('click', function(){

            var clonedval = $('.payment_field .payment_field_row .payment_first_step').html();

            $('.payment_field .payment_field_row').append('<div class="payment_field_col payment_field_clone">'+clonedval+'</div>');

        });



        $('.add_fee_type a.fee_type_btn').on('click', function(){

            var clonedval = $('.fees_type_sec .fee_type_row .fees_type_col').html();

            $('.fees_type_sec .fee_type_row').append('<div class="custom_type_col fees_type_clone">'+clonedval+'</div>');

        });



        $(document).delegate('.payment_field_col .field_remove_col a.remove_col', 'click', function(){

            var $tr    = $(this).closest('.payment_field_clone');

            var trclone = $('.payment_field_clone').length;

            if(trclone > 0){

                $tr.remove();

                grandtotal();

            }

        });

        $(document).delegate('.fees_type_sec .fee_type_row .fees_type_clone a.remove_btn', 'click', function(){

            var $tr    = $(this).closest('.fees_type_clone');

            var trclone = $('.fees_type_clone').length;

            if(trclone > 0){

                $tr.remove();

                grandtotal();

            }

        });



        // Handle application ID from query parameter

        var urlParams = new URLSearchParams(window.location.search);

        var appliid = urlParams.get('appid') || window.ClientDetailConfig.appId;

        if (appliid) {

            $('.if_applicationdetail').hide();

            $('#application-tab').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.getApplicationDetail,

                type:'GET',

                data:{id:appliid},

                success:function(response){

                    $('.popuploader').hide();

                    $('#application-tab').html(response);

                    $('.datepicker').daterangepicker({

                    locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },

                                    singleDatePicker: true,



                                    showDropdowns: true,

                    }, function(start, end, label) {

                        $('#popuploader').show();

                        $.ajax({

                            url: window.ClientDetailConfig.urls.updateIntake,

                            method: "GET", // or POST

                            dataType: "json",

                            data: {from: start.format('YYYY-MM-DD'), appid: appliid},

                            success:function(result) {

                                $('#popuploader').hide();

                                

                            }

                        });

                    });



                    $('.expectdatepicker').daterangepicker({

                    locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },

                                    singleDatePicker: true,



                                    showDropdowns: true,

                    }, function(start, end, label) {

                        $('#popuploader').show();

                        $.ajax({

                            url: window.ClientDetailConfig.urls.updateExpectWin,

                            method: "GET", // or POST

                            dataType: "json",

                            data: {from: start.format('YYYY-MM-DD'), appid: appliid},

                            success:function(result) {

                                $('#popuploader').hide();

                            }

                        });

                    });



                    $('.startdatepicker').daterangepicker({

                    locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },

                                    singleDatePicker: true,



                                    showDropdowns: true,

                    }, function(start, end, label) {

                        $('#popuploader').show();

                        $.ajax({

                            url: window.ClientDetailConfig.urls.updateDates,

                            method: "GET", // or POST

                            dataType: "json",

                            data: {from: start.format('YYYY-MM-DD'), appid: appliid, datetype: 'start'},

                            success:function(result) {

                                $('#popuploader').hide();

                                    var obj = result;

                                if(obj.status){

                                    $('.app_start_date .month').html(obj.dates.month);

                                    $('.app_start_date .day').html(obj.dates.date);

                                    $('.app_start_date .year').html(obj.dates.year);

                                }

                            }

                        });

                    });



                    $('.enddatepicker').daterangepicker({

                    locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },

                                    singleDatePicker: true,



                                    showDropdowns: true,

                    }, function(start, end, label) {

                        $('#popuploader').show();

                        $.ajax({

                            url: window.ClientDetailConfig.urls.updateDates,

                            method: "GET", // or POST

                            dataType: "json",

                            data: {from: start.format('YYYY-MM-DD'), appid: appliid, datetype: 'end'},

                            success:function(result) {

                                $('#popuploader').hide();

                                    var obj =result;

                                if(obj.status){

                                    $('.app_end_date .month').html(obj.dates.month);

                                    $('.app_end_date .day').html(obj.dates.date);

                                    $('.app_end_date .year').html(obj.dates.year);

                                }

                            }

                        });

                    });



                }

            });

        }



        $(document).delegate('.discon_application', 'click', function(){

            var appliid = $(this).attr('data-id');

            $('#discon_application').modal('show');

            $('input[name="diapp_id"]').val(appliid);

        });



        $(document).delegate('.revertapp', 'click', function(){

            var appliid = $(this).attr('data-id');

            $('#revert_application').modal('show');

            $('input[name="revapp_id"]').val(appliid);

        });



        $(document).delegate('.completestage', 'click', function(){

            var appliid = $(this).attr('data-id');

            $('#confirmcompleteModal').modal('show');

            $('.acceptapplication').attr('data-id',appliid)



        });



        $(document).delegate('.openapplicationdetail', 'click', function(){

            var clientMatterId = $(this).attr('data-id');

            $('.if_applicationdetail').hide();

            $('#application-tab').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.getApplicationDetail,

                type:'GET',

                data:{id:clientMatterId},

                success:function(response){

                    $('.popuploader').hide();

                    $('#application-tab').html(response);

                    $('.datepicker').daterangepicker({

                        locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },

                        singleDatePicker: true,

                        showDropdowns: true,

                    }, function(start, end, label) {

                        $('#popuploader').show();

                        $.ajax({

                            url: window.ClientDetailConfig.urls.updateIntake,

                            method: "GET", // or POST

                            dataType: "json",

                            data: {from: start.format('YYYY-MM-DD'), appid: clientMatterId},

                            success:function(result) {

                                $('#popuploader').hide();

                            }

                        });

                    });



                    $('.expectdatepicker').daterangepicker({

                        locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },

                        singleDatePicker: true,

                        showDropdowns: true,

                    }, function(start, end, label) {

                        $('#popuploader').show();

                        $.ajax({

                            url: window.ClientDetailConfig.urls.updateExpectWin,

                            method: "GET", // or POST

                            dataType: "json",

                            data: {from: start.format('YYYY-MM-DD'), appid: clientMatterId},

                            success:function(result) {

                                $('#popuploader').hide();

                            }

                        });

                    });



                    $('.startdatepicker').daterangepicker({

                        locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },

                        singleDatePicker: true,

                        showDropdowns: true,

                    }, function(start, end, label) {

                        $('#popuploader').show();

                        $.ajax({

                            url: window.ClientDetailConfig.urls.updateDates,

                            method: "GET", // or POST

                            dataType: "json",

                            data: {from: start.format('YYYY-MM-DD'), appid: clientMatterId, datetype: 'start'},

                            success:function(result) {

                                $('#popuploader').hide();

                                var obj = result;

                                if(obj.status){

                                    $('.app_start_date .month').html(obj.dates.month);

                                    $('.app_start_date .day').html(obj.dates.date);

                                    $('.app_start_date .year').html(obj.dates.year);

                                }

                            }

                        });

                    });



                    $('.enddatepicker').daterangepicker({

                        locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },

                        singleDatePicker: true,

                        showDropdowns: true,

                    }, function(start, end, label) {

                        $('#popuploader').show();

                        $.ajax({

                            url: window.ClientDetailConfig.urls.updateDates,

                            method: "GET", // or POST

                            dataType: "json",

                            data: {from: start.format('YYYY-MM-DD'), appid: clientMatterId, datetype: 'end'},

                            success:function(result) {

                                $('#popuploader').hide();

                                var obj = result;

                                if(obj.status){

                                    $('.app_end_date .month').html(obj.dates.month);

                                    $('.app_end_date .day').html(obj.dates.date);

                                    $('.app_end_date .year').html(obj.dates.year);

                                }

                            }

                        });

                    });

                },

                error: function(xhr, status, error) {

                    console.error('Error loading client matter details:', error);

                    $('#application-tab').html('<h4>Error loading client matter details. Please try again.</h4>');

                }

            });

        });





        $(document).delegate('#application-tab,#stages-tab', 'click', function(){

            $('.if_applicationdetail').show();

            $('#application-tab').show().html('<h4>Please wait ...</h4>');

        });



        



        $(document).delegate('.openappnote', 'click', function(){

            var apptype = $(this).attr('data-app-type');

            var id = $(this).attr('data-id');

            $('#create_applicationnote #noteid').val(id);

            $('#create_applicationnote #type').val(apptype);

            $('#create_applicationnote').modal('show');

        });

        $(document).delegate('.openappappoint', 'click', function(){

            var id = $(this).attr('data-id');

            var apptype = $(this).attr('data-app-type');

            $('#create_applicationappoint #type').val(apptype);

            $('#create_applicationappoint #appointid').val(id);

            $('#create_applicationappoint').modal('show');

        });



        $(document).delegate('.openclientemail', 'click', function(){

            var id = $(this).attr('data-id');

            var apptype = $(this).attr('data-app-type');

            $('#applicationemailmodal #type').val(apptype);

            $('#applicationemailmodal #appointid').val(id);

            $('#applicationemailmodal').modal('show');

        });



        $(document).delegate('.openchecklist', 'click', function(){

            var id = $(this).attr('data-id');

            var type = $(this).attr('data-type');

            var typename = $(this).attr('data-typename');

            $('#create_checklist #checklistapp_id').val(id);

            $('#create_checklist #checklist_type').val(type);

            $('#create_checklist #checklist_typename').val(typename);

            $('#create_checklist').modal('show');

        });



        $(document).delegate('.openpaymentschedule', 'click', function(){

            var id = $(this).attr('data-id');

            //$('#create_apppaymentschedule #application_id').val(id);

            $('#addpaymentschedule').modal('show');

            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.addScheduleInvoiceDetail,

                type: 'GET',

                data: {id: $(this).attr('data-id')},

                success: function(res){

                    $('.popuploader').hide();

                    $('.showpoppaymentscheduledata').html(res);

                    $(".datepicker").daterangepicker({

                        locale: { format: "YYYY-MM-DD" },

                        singleDatePicker: true,

                        showDropdowns: true

                    });

                }

            });

        });



        $(document).delegate('.addfee', 'click', function(){

            var clonedval = $('.feetypecopy').html();

            $('.fee_type_sec .fee_fields').append('<div class="fee_fields_row field_clone">'+clonedval+'</div>');

        });



        $(document).delegate('.payremoveitems', 'click', function(){

            $(this).parent().parent().remove();

            schedulecalculatetotal();

        });



        $(document).delegate('.payfee_amount', 'keyup', function(){

            schedulecalculatetotal();

        });



        $(document).delegate('.paydiscount', 'keyup', function(){

            schedulecalculatetotal();

        });



        function schedulecalculatetotal(){

            var feeamount = 0;

            $('.payfee_amount').each(function(){

                if($(this).val() != ''){

                    feeamount += parseFloat($(this).val());

                }

            });

            var discount = 0;

            if($('.paydiscount').val() != ''){

                discount = $('.paydiscount').val();

            }

            var netfee = feeamount - parseFloat(discount);

            $('.paytotlfee').html(feeamount.toFixed(2));

            $('.paynetfeeamt').html(netfee.toFixed(2));

        }



        $(document).delegate('.createaddapointment', 'click', function(){

            $('#create_appoint').modal('show');

        });



        $(document).delegate('.openfileupload', 'click', function(){

            var id = $(this).attr('data-id');

            var type = $(this).attr('data-type');

            var typename = $(this).attr('data-typename');

            var aid = $(this).attr('data-aid');

            $(".checklisttype").val(type);

            $(".checklistid").val(id);

            $(".checklisttypename").val(typename);

            $(".application_id").val(aid);

            $('#openfileuploadmodal').modal('show');

        });



        $(document).delegate('.opendocnote', 'click', function(){

            var id = '';

            var type = $(this).attr('data-app-type');

            var aid = $(this).attr('data-id');

            $(".checklisttype").val(type);

            $(".checklistid").val(id);

            $(".application_id").val(aid);

            $('#openfileuploadmodal').modal('show');

        });



        $(document).delegate('.due_date_sec a.due_date_btn', 'click', function(){

            $('.due_date_sec .due_date_col').show();

            $(this).hide();

            $('.checklistdue_date').val(1);

        });



        $(document).delegate('.remove_col a.remove_btn', 'click', function(){

            $('.due_date_sec .due_date_col').hide();

            $('.due_date_sec a.due_date_btn').show();

            $('.checklistdue_date').val(0);

        });



        $(document).delegate('.nextstage', 'click', function(){

            var appliid = $(this).attr('data-id');

            var stage = $(this).attr('data-stage');

            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.updateStage,

                type:'GET',

                datatype:'json',

                data:{id:appliid, client_id:window.ClientDetailConfig.clientId},

                success:function(response){

                    $('.popuploader').hide();

                    var obj = $.parseJSON(response);

                    if(obj.status){

                        $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');

                        $('.curerentstage').text(obj.stage);

                        $('.progress-circle span').html(obj.width+' %');

                        var over = '';

                        if(obj.width > 50){

                            over = '50';

                        }

                        $("#progresscir").removeClass();

                        $("#progresscir").addClass('progress-circle');

                        $("#progresscir").addClass('prgs_'+obj.width);

                        $("#progresscir").addClass('over_'+over);

                        if(obj.displaycomplete){



                            $('.completestage').show();

                            $('.nextstage').hide();

                        }

                        $.ajax({

                            url: site_url+'/get-applications-logs',

                            type:'GET',

                            data:{clientid:window.ClientDetailConfig.clientId,id: appliid},

                            success: function(responses){



                                $('#accordion').html(responses);

                            }

                        });

                    }else{

                        $('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');

                    }

                }

            });

        });



        $(document).delegate('.acceptapplication', 'click', function(){

            var appliid = $(this).attr('data-id');



            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.completeStage,

                type:'GET',

                datatype:'json',

                data:{id:appliid, client_id:window.ClientDetailConfig.clientId},

                success:function(response){

                    $('.popuploader').hide();

                    var obj = $.parseJSON(response);

                    if(obj.status){

                        $('.progress-circle span').html(obj.width+' %');

                        var over = '';

                        if(obj.width > 50){

                            over = '50';

                        }

                        $("#progresscir").removeClass();

                        $("#progresscir").addClass('progress-circle');

                        $("#progresscir").addClass('prgs_'+obj.width);

                        $("#progresscir").addClass('over_'+over);

                        $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');

                            $('.ifdiscont').hide();

                            $('.revertapp').show();

                        $('#confirmcompleteModal').modal('hide');

                        $.ajax({

                                url: site_url+'/get-applications-logs',

                                type:'GET',

                                data:{clientid:window.ClientDetailConfig.clientId,id: appliid},

                                success: function(responses){



                                    $('#accordion').html(responses);

                                }

                            });

                    }else{

                        $('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');

                    }

                }

            });

        });



        $(document).delegate('.backstage', 'click', function(){

            var appliid = $(this).attr('data-id');

            var stage = $(this).attr('data-stage');

            if(stage == 'Application'){



            }else{

                $('.popuploader').show();

                $.ajax({

                    url: window.ClientDetailConfig.urls.updateBackStage,

                    type:'GET',

                    datatype:'json',

                    data:{id:appliid, client_id:window.ClientDetailConfig.clientId},

                    success:function(response){

                        var obj = $.parseJSON(response);

                        $('.popuploader').hide();

                        if(obj.status){

                            $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');

                            $('.curerentstage').text(obj.stage);

                            $('.progress-circle span').html(obj.width+' %');

                        var over = '';

                        if(obj.width > 50){

                            over = '50';

                        }

                        $("#progresscir").removeClass();

                        $("#progresscir").addClass('progress-circle');

                        $("#progresscir").addClass('prgs_'+obj.width);

                        $("#progresscir").addClass('over_'+over);

                            if(obj.displaycomplete == false){

                                $('.completestage').hide();

                                $('.nextstage').show();

                            }

                            $.ajax({

                                url: site_url+'/get-applications-logs',

                                type:'GET',

                                data:{clientid:window.ClientDetailConfig.clientId,id: appliid},

                                success: function(responses){



                                    $('#accordion').html(responses);

                                }

                            });

                        }else{

                            $('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');

                        }

                    }

                });

            }

        });





        $(document).delegate('#notes-tab', 'click', function(){

            var appliid = $(this).attr('data-id');

            $('.if_applicationdetail').hide();

            $('#application-tab').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.getApplicationNotes,

                type:'GET',

                data:{id:appliid},

                success:function(response){

                    $('.popuploader').hide();

                    $('#notes').html(response);

                }

            });

        });



        $(".timezoneselects2").select2({

            dropdownParent: $("#create_appoint")

        });



        $(".Inviteesselects2").select2({

            dropdownParent: $("#create_appoint")

        });



        $(".assignee").select2({

            dropdownParent: $("#opentaskmodal")

        });



        $(".timezoneselects2").select2({

            dropdownParent: $("#create_applicationappoint")

        });



        $('#attachments').on('change',function(){

            // output raw value of file input

            $('.showattachment').html('');

            // or, manipulate it further with regex etc.

            var filename = $(this).val().replace(/.*(\/|\\)/, '');

            // .. do your magic

            $('.showattachment').html(filename);

        });



        $(document).delegate('.opensuperagent', 'click', function(){

            var appid = $(this).attr('data-id');

            $('#superagent_application').modal('show');

            $('#superagent_application #siapp_id').val(appid);

        });



        $(document).delegate('.opentagspopup', 'click', function(){

            var appid = $(this).attr('data-id');

            $('#tags_clients').modal('show');

            $('#tags_clients #client_id').val(appid);

            $(".tagsselec").select2({ tags: true, dropdownParent: $("#tags_clients .modal-content") });

        });



        $(document).delegate('.serviceTaken','click', function(){

            $('#serviceTaken').modal('show');

        });



        $(document).delegate('.opensubagent', 'click', function(){

            var appid = $(this).attr('data-id');

            $('#subagent_application').modal('show');

            $('#subagent_application #sbapp_id').val(appid);

        });





        $(document).delegate('.removesuperagent', 'click', function(){

            var appid = $(this).attr('data-id');

        });



        $(document).delegate('.application_ownership', 'click', function(){

            var appid = $(this).attr('data-id');

            var ration = $(this).attr('data-ration');

            $('#application_ownership #mapp_id').val(appid);

            $('#application_ownership .sus_agent').val($(this).attr('data-name'));

            $('#application_ownership .ration').val(ration);

            $('#application_ownership').modal('show');

        });



        $(document).delegate('.opensaleforcast', 'click', function(){

            var fapp_id = $(this).attr('data-id');

            var client_revenue = $(this).attr('data-client_revenue');

            var partner_revenue = $(this).attr('data-partner_revenue');

            var discounts = $(this).attr('data-discounts');

            $('#application_opensaleforcast #fapp_id').val(fapp_id);

            $('#application_opensaleforcast #client_revenue').val(client_revenue);

            $('#application_opensaleforcast #partner_revenue').val(partner_revenue);

            $('#application_opensaleforcast #discounts').val(discounts);

            $('#application_opensaleforcast').modal('show');

        });





        $(document).delegate('.opensaleforcastservice', 'click', function(){

            var fapp_id = $(this).attr('data-id');

            var client_revenue = $(this).attr('data-client_revenue');

            var partner_revenue = $(this).attr('data-partner_revenue');

            var discounts = $(this).attr('data-discounts');

            $('#application_opensaleforcastservice #fapp_id').val(fapp_id);

            $('#application_opensaleforcastservice #client_revenue').val(client_revenue);

            $('#application_opensaleforcastservice #partner_revenue').val(partner_revenue);

            $('#application_opensaleforcastservice #discounts').val(discounts);

            $('#interest_service_view').modal('hide');

            $('#application_opensaleforcastservice').modal('show');

        });



        $(document).delegate('.closeservmodal', 'click', function(){

            $('#interest_service_view').modal('hide');

            $('#application_opensaleforcastservice').modal('hide');

        });



        $(document).on("hidden.bs.modal", "#application_opensaleforcastservice", function (e) {

            $('body').addClass('modal-open');

        });



	    $(document).delegate('#new_fee_option .fee_option_addbtn a', 'click', function(){

		    var html = '<tr class="add_fee_option cus_fee_option"><td><select data-valid="required" class="form-control course_fee_type" name="course_fee_type[]"><option value="">Select Type</option><option value="Accommodation Fee">Accommodation Fee</option><option value="Administration Fee">Administration Fee</option><option value="Application Fee">Application Fee</option><option value="Bond">Bond</option></select></td><td><input type="number" value="0" class="form-control semester_amount" name="semester_amount[]"></td><td><input type="number" value="1" class="form-control no_semester" name="no_semester[]"></td><td class="total_fee"><span>0.00</span><input type="hidden"  class="form-control total_fee_am" value="0" name="total_fee[]"></td><td><input type="number" value="1" class="form-control claimable_terms" name="claimable_semester[]"></td><td><input type="number" class="form-control commission" name="commission[]"></td><td> <a href="javascript:;" class="removefeetype"><i class="fa fa-trash"></i></a></td></tr>';

		    $('#new_fee_option #productitemview tbody').append(html);

        });



        $(document).delegate('#new_fee_option .removefeetype', 'click', function(){

            $(this).parent().parent().remove();



            var price = 0;

            $('#new_fee_option .total_fee_am').each(function(){

                price += parseFloat($(this).val());

            });



            var discount_sem = $('.discount_sem').val();

            var discount_amount = $('.discount_amount').val();

            var cservd = 0.00;

            if(discount_sem != ''){

                cservd = discount_sem;

            }



            var cservs = 0.00;

            if(discount_amount != ''){

                cservs = discount_amount;

            }

            var dis = parseFloat(cservs) * parseFloat(cservd);

            var duductdis = price - dis;



            $('#new_fee_option .net_totl').html(duductdis.toFixed(2));

        });





        $(document).delegate('#new_fee_option .semester_amount','keyup', function(){

            var installment_amount = $(this).val();

            var cserv = 0.00;

            if(installment_amount != ''){

                cserv = installment_amount;

            }



            var installment = $(this).parent().parent().find('.no_semester').val();



            var totalamount = parseFloat(cserv) * parseInt(installment);

            $(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));

            $(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));

            var price = 0;

            $('#new_fee_option .total_fee_am').each(function(){

                price += parseFloat($(this).val());

            });



            var discount_sem = $('.discount_sem').val();

            var discount_amount = $('.discount_amount').val();

            var cservd = 0.00;

            if(discount_sem != ''){

                cservd = discount_sem;

            }



            var cservs = 0.00;

            if(discount_amount != ''){

                cservs = discount_amount;

            }

            var dis = parseFloat(cservs) * parseFloat(cservd);

            var duductdis = price - dis;



            $('#new_fee_option .net_totl').html(duductdis.toFixed(2));

        });





        $(document).delegate('#new_fee_option .no_semester','keyup', function(){

            var installment = $(this).val();





            var installment_amount = $(this).parent().parent().find('.semester_amount').val();

            var cserv = 0.00;

            if(installment_amount != ''){

                cserv = installment_amount;

            }

            var totalamount = parseFloat(cserv) * parseInt(installment);

            $(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));

            $(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));

            var price = 0;

            $('#new_fee_option .total_fee_am').each(function(){

                price += parseFloat($(this).val());

            });



            var discount_sem = $('.discount_sem').val();

            var discount_amount = $('.discount_amount').val();

            var cservd = 0.00;

            if(discount_sem != ''){

                cservd = discount_sem;

            }



            var cservs = 0.00;

            if(discount_amount != ''){

                cservs = discount_amount;

            }

            var dis = parseFloat(cservs) * parseFloat(cservd);

            var duductdis = price - dis;



            $('#new_fee_option .net_totl').html(duductdis.toFixed(2));

        });



        $(document).delegate('#new_fee_option .discount_amount','keyup', function(){

            var discount_amount = $(this).val();

            var discount_sem = $('.discount_sem').val();

            var cserv = 0.00;

            if(discount_sem != ''){

                cserv = discount_sem;

            }



            var cservs = 0.00;

            if(discount_amount != ''){

                cservs = discount_amount;

            }

            var dis = parseFloat(cservs) * parseFloat(cserv);

            $('.totaldis span').html(dis.toFixed(2));

            var price = 0;

            $('#new_fee_option .total_fee_am').each(function(){

                price += parseFloat($(this).val());

            });

            var duductdis = price - dis;

            $('#new_fee_option .net_totl').html(duductdis.toFixed(2));

            $('.totaldis .total_dis_am').val(dis.toFixed(2));



        });



        $(document).delegate('#new_fee_option .discount_sem','keyup', function(){

            var discount_sem = $(this).val();

            var discount_amount = $('.discount_amount').val();

            var cserv = 0.00;

            if(discount_sem != ''){

                cserv = discount_sem;

            }



            var cservs = 0.00;

            if(discount_amount != ''){

                cservs = discount_amount;

            }

            var dis = parseFloat(cservs) * parseFloat(cserv);

            $('.totaldis span').html(dis.toFixed(2));

            $('.totaldis .total_dis_am').val(dis.toFixed(2));



            var price = 0;

            $('#new_fee_option .total_fee_am').each(function(){

                price += parseFloat($(this).val());

            });

            var duductdis = price - dis;

            $('#new_fee_option .net_totl').html(duductdis.toFixed(2));



        });



        $(document).delegate('.editpaymentschedule', 'click', function(){

            $('#editpaymentschedule').modal('show');

            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.scheduleInvoiceDetail,

                type: 'GET',

                data: {id: $(this).attr('data-id'),t:'application'},

                success: function(res){

                    $('.popuploader').hide();

                    $('.showeditmodule').html(res);

                    $(".editclientname").select2({

                        dropdownParent: $("#editpaymentschedule .modal-content")

                    });



                    $(".datepicker").daterangepicker({

                        locale: { format: "YYYY-MM-DD" },

                        singleDatePicker: true,

                        showDropdowns: true

                    });

                }

            });

        });



    });



    $(document).ready(function() {

        $(document).delegate("#ddArea", "dragover", function() {

          $(this).addClass("drag_over");

          return false;

        });



        $(document).delegate("#ddArea", "dragleave", function() {

          $(this).removeClass("drag_over");

          return false;

        });



        $(document).delegate("#ddArea", "click", function(e) {

          file_explorer();

        });



        $(document).delegate("#ddArea", "drop", function(e) {

            e.preventDefault();

            $(this).removeClass("drag_over");

            var formData = new FormData();

            var files = e.originalEvent.dataTransfer.files;

            for (var i = 0; i < files.length; i++) {

                formData.append("file[]", files[i]);

            }

            uploadFormData(formData);

        });



        function file_explorer() {

            document.getElementById("selectfile").click();

            document.getElementById("selectfile").onchange = function() {

                files = document.getElementById("selectfile").files;

                var formData = new FormData();



                for (var i = 0; i < files.length; i++) {

                formData.append("file[]", files[i]);

                }

                formData.append("type", $('.checklisttype').val());

                formData.append("typename", $('.checklisttypename').val());

                formData.append("id", $('.checklistid').val());

                formData.append("application_id", $('.application_id').val());



                uploadFormData(formData);

            };

        }



        function uploadFormData(form_data) {

            $('.popuploader').show();

            $.ajax({

                url: window.ClientDetailConfig.urls.checklistUpload,

                method: "POST",

                data: form_data,

                datatype: 'json',

                contentType: false,

                cache: false,

                processData: false,

                success: function(response) {

				    var obj = $.parseJSON(response);

                    $('.popuploader').hide();

                    $('#openfileuploadmodal').modal('hide');

                    $('.mychecklistdocdata').html(obj.doclistdata);

                    $('.checklistuploadcount').html(obj.applicationuploadcount);

			        $('.'+obj.type+'_checklists').html(obj.checklistdata);

			        $('#selectfile').val('');

                }

            });

        }





        $(document).delegate('#new_fee_option_serv .fee_option_addbtn a', 'click', function(){

            var html = '<tr class="add_fee_option cus_fee_option"><td><select data-valid="required" class="form-control course_fee_type" name="course_fee_type[]"><option value="">Select Type</option><option value="Accommodation Fee">Accommodation Fee</option><option value="Administration Fee">Administration Fee</option><option value="Application Fee">Application Fee</option><option value="Bond">Bond</option></select></td><td><input type="number" value="0" class="form-control semester_amount" name="semester_amount[]"></td><td><input type="number" value="1" class="form-control no_semester" name="no_semester[]"></td><td class="total_fee"><span>0.00</span><input type="hidden"  class="form-control total_fee_am" value="0" name="total_fee[]"></td><td><input type="number" value="1" class="form-control claimable_terms" name="claimable_semester[]"></td><td><input type="number" class="form-control commission" name="commission[]"></td><td> <a href="javascript:;" class="removefeetype"><i class="fa fa-trash"></i></a></td></tr>';

            $('#new_fee_option_serv #productitemview tbody').append(html);

        });



        $(document).delegate('#new_fee_option_serv .removefeetype', 'click', function(){

            $(this).parent().parent().remove();



            var price = 0;

            $('#new_fee_option_serv .total_fee_am').each(function(){

                price += parseFloat($(this).val());

            });



            var discount_sem = $('#new_fee_option_serv .discount_sem').val();

            var discount_amount = $('#new_fee_option_serv .discount_amount').val();

            var cservd = 0.00;

            if(discount_sem != ''){

                cservd = discount_sem;

            }



            var cservs = 0.00;

            if(discount_amount != ''){

                cservs = discount_amount;

            }

            var dis = parseFloat(cservs) * parseFloat(cservd);

            var duductdis = price - dis;



            $('#new_fee_option_serv .net_totl').html(duductdis.toFixed(2));

        });





        $(document).delegate('#new_fee_option_serv .semester_amount','keyup', function(){

            var installment_amount = $(this).val();

            var cserv = 0.00;

            if(installment_amount != ''){

                cserv = installment_amount;

            }



            var installment = $(this).parent().parent().find('.no_semester').val();



            var totalamount = parseFloat(cserv) * parseInt(installment);

            $(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));

            $(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));

            var price = 0;

            $('#new_fee_option_serv .total_fee_am').each(function(){

                price += parseFloat($(this).val());

            });



            var discount_sem = $('#new_fee_option_serv .discount_sem').val();

            var discount_amount = $('#new_fee_option_serv .discount_amount').val();

            var cservd = 0.00;

            if(discount_sem != ''){

                cservd = discount_sem;

            }



            var cservs = 0.00;

            if(discount_amount != ''){

                cservs = discount_amount;

            }

            var dis = parseFloat(cservs) * parseFloat(cservd);

            var duductdis = price - dis;



            $('#new_fee_option_serv .net_totl').html(duductdis.toFixed(2));

        });





        $(document).delegate('#new_fee_option_serv .no_semester','keyup', function(){

            var installment = $(this).val();





            var installment_amount = $(this).parent().parent().find('.semester_amount').val();

            var cserv = 0.00;

            if(installment_amount != ''){

                cserv = installment_amount;

            }

            var totalamount = parseFloat(cserv) * parseInt(installment);

            $(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));

            $(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));

            var price = 0;

            $('#new_fee_option_serv .total_fee_am').each(function(){

                price += parseFloat($(this).val());

            });



            var discount_sem = $('.discount_sem').val();

            var discount_amount = $('.discount_amount').val();

            var cservd = 0.00;

            if(discount_sem != ''){

                cservd = discount_sem;

            }



            var cservs = 0.00;

            if(discount_amount != ''){

                cservs = discount_amount;

            }

            var dis = parseFloat(cservs) * parseFloat(cservd);

            var duductdis = price - dis;



            $('#new_fee_option_serv .net_totl').html(duductdis.toFixed(2));

        });



        $(document).delegate('#new_fee_option_serv .discount_amount','keyup', function(){

            var discount_amount = $(this).val();

            var discount_sem = $('#new_fee_option_serv .discount_sem').val();

            var cserv = 0.00;

            if(discount_sem != ''){

                cserv = discount_sem;

            }



            var cservs = 0.00;

            if(discount_amount != ''){

                cservs = discount_amount;

            }

            var dis = parseFloat(cservs) * parseFloat(cserv);

            $('#new_fee_option_serv .totaldis span').html(dis.toFixed(2));

            var price = 0;

            $('#new_fee_option_serv .total_fee_am').each(function(){

                price += parseFloat($(this).val());

            });

            var duductdis = price - dis;

            $('#new_fee_option_serv .net_totl').html(duductdis.toFixed(2));

            $('#new_fee_option_serv .totaldis .total_dis_am').val(dis.toFixed(2));



        });



        $(document).delegate('#new_fee_option_serv .discount_sem','keyup', function(){

            var discount_sem = $(this).val();

            var discount_amount = $('#new_fee_option_serv .discount_amount').val();

            var cserv = 0.00;

            if(discount_sem != ''){

                cserv = discount_sem;

            }



            var cservs = 0.00;

            if(discount_amount != ''){

                cservs = discount_amount;

            }

            var dis = parseFloat(cservs) * parseFloat(cserv);

            $('#new_fee_option_serv .totaldis span').html(dis.toFixed(2));

            $('#new_fee_option_serv .totaldis .total_dis_am').val(dis.toFixed(2));



            var price = 0;

            $('#new_fee_option_serv .total_fee_am').each(function(){

                price += parseFloat($(this).val());

            });

            var duductdis = price - dis;

            $('#new_fee_option_serv .net_totl').html(duductdis.toFixed(2));



        });

    });



    function arcivedAction( id, table ) {

		var conf = confirm('Are you sure, you would like to delete this record. Remember all Related data would be deleted.');

		if(conf){

			if(id == '') {

				alert('Please select ID to delete the record.');

				return false;

			} else {

				$('#popuploader').show();

				$(".server-error").html(''); //remove server error.

				$(".custom-error-msg").html(''); //remove custom error.

				$.ajax({

					type:'post',

					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

					url: window.ClientDetailConfig.urls.deleteAction,

					data:{'id': id, 'table' : table},

					success:function(resp) {

						$('#popuploader').hide();

						var obj = $.parseJSON(resp);

						if(obj.status == 1) {

							location.reload();



						} else{

							var html = errorMessage(obj.message);

							$(".custom-error-msg").html(html);

						}

						$("#popuploader").hide();

					},

					beforeSend: function() {

						$("#popuploader").show();

					}

				});

				$('html, body').animate({scrollTop:0}, 'slow');

			}

		} else{

			$("#loader").hide();

		}

	}



	



	// Run test on page load

	$(document).ready(function() {

		

		

		// Ensure renamechecklist elements are clickable

		$('.renamechecklist').css({

			'pointer-events': 'auto',

			'cursor': 'pointer',

			'z-index': '1000'

		});

		

		// Add hover effect to make it clear they're clickable

		$('.renamechecklist').hover(

			function() {

				$(this).css('background-color', '#f8f9fa');

			},

			function() {

				$(this).css('background-color', '');

			}

		);

		

		// Direct event binding for renamechecklist elements

		$('.renamechecklist').off('click').on('click', function(e) {

			e.preventDefault();

			e.stopPropagation();

			

		

			

			var $this = $(this);

			var $drow = $this.closest('.drow');

			var $parent = $drow.find('.personalchecklist-row');

			

			if ($parent.length === 0) {

				console.error('Personal checklist row not found in direct handler');

				return false;

			}

			

			var opentime = $parent.data('personalchecklistname');

			if (!opentime) {

				console.error('Personal checklist name not found in direct handler');

				return false;

			}

			

			$parent.data('current-html', $parent.html());

			$parent.empty().append(

				$('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),

				$('<button class="btn btn-personalprimary btn-sm mb-1"><i class="fas fa-check"></i></button>'),

				$('<button class="btn btn-personaldanger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')

			);

			

			

			return false;

		});



		// Direct event binding for renamedoc elements

		$('.renamedoc').off('click').on('click', function(e) {

			e.preventDefault();

			e.stopPropagation();

			

		

			

			var $this = $(this);

			var $drow = $this.closest('.drow');

			var $parent = $drow.find('.doc-row');

			

			if ($parent.length === 0) {

				console.error('Document row not found in direct handler');

				return false;

			}

			

			var opentime = $parent.data('name');

			if (!opentime) {

				console.error('Document name not found in direct handler');

				return false;

			}

			

			$parent.data('current-html', $parent.html());

			$parent.empty().append(

				$('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),

				$('<button class="btn btn-primary btn-sm mb-1"><i class="fas fa-check"></i></button>'),

				$('<button class="btn btn-danger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')

			);

			

			return false;

		});



		// Direct event binding for visa checklist rename elements

		$('.migdocumnetlist1 .renamechecklist').off('click').on('click', function(e) {

			e.preventDefault();

			e.stopPropagation();

			

			var $this = $(this);

			var $drow = $this.closest('.drow');

			var $parent = $drow.find('.visachecklist-row');

			

			if ($parent.length === 0) {

				console.error('Visa checklist row not found in direct handler');

				return false;

			}

			

			var opentime = $parent.data('visachecklistname');

			if (!opentime) {

				console.error('Visa checklist name not found in direct handler');

				return false;

			}

			

			$parent.data('current-html', $parent.html());

			$parent.empty().append(

				$('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),

				$('<button class="btn btn-visaprimary btn-sm mb-1"><i class="fas fa-check"></i></button>'),

				$('<button class="btn btn-visadanger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')

			);

			

			return false;

		});



		// Ensure download-file elements are clickable

		$('.download-file').css({

			'pointer-events': 'auto',

			'cursor': 'pointer',

			'z-index': '1000'

		});

		

		// Add hover effect to download-file elements

		$('.download-file').hover(

			function() {

				$(this).css('background-color', '#f8f9fa');

			},

			function() {

				$(this).css('background-color', '');

			}

		);



		// Direct event binding for download-file elements

		$('.download-file').off('click').on('click', function(e) {

			e.preventDefault();

			e.stopPropagation();

			

			const $this = $(this);

			const filelink = $this.data('filelink');

			const filename = $this.data('filename');

			

			if (!filelink || !filename) {

				console.error('Direct handler - Missing file info');

				alert('Missing file info. Please try again.');

				return false;

			}

			

			// Show loading indicator

			$this.html('<i class="fas fa-spinner fa-spin"></i> Downloading...');

			$this.prop('disabled', true);

			

			// Create and submit a hidden form

			const form = $('<form>', {

				method: 'POST',

				action: window.ClientDetailConfig.urls.downloadDocument,

				target: '_blank',

				style: 'display: none;'

			});

			

			// CSRF token

			const token = $('meta[name="csrf-token"]').attr('content');

			if (!token) {

				console.error('Direct handler - CSRF token not found');

				alert('Security token not found. Please refresh the page and try again.');

				$this.html('Download').prop('disabled', false);

				return false;

			}

			

			// Add form fields

			form.append($('<input>', {

				type: 'hidden',

				name: '_token',

				value: token

			}));

			

			form.append($('<input>', {

				type: 'hidden',

				name: 'filelink',

				value: filelink

			}));

			

			form.append($('<input>', {

				type: 'hidden',

				name: 'filename',

				value: filename

			}));

			

			// Append form to body and submit

			$('body').append(form);

			

			try {

				form[0].submit();

				// Reset button after a short delay

				setTimeout(function() {

					$this.html('Download').prop('disabled', false);

				}, 2000);

				

			} catch (error) {

				console.error('Direct handler - Error submitting form:', error);

				alert('Error initiating download. Please try again.');

				$this.html('Download').prop('disabled', false);

			}

			

			// Remove form after submission

			setTimeout(function() {

				form.remove();

			}, 1000);

			

			return false;

		});

		

		// Function to open signature link in new tab

		function openSignatureLink(url) {

			try {

				// Method 1: Try window.open

				const newWindow = window.open(url, '_blank');

				if (newWindow) {

					newWindow.focus();

					return true;

				}

				

				// Method 2: If window.open fails, try location.href

				window.location.href = url;

				return true;

				

			} catch (error) {

				console.error('Error opening signature link:', error);

				alert('Unable to open signature link. Please try again or copy the URL manually.');

				return false;

			}

		}



		// Send to Hubdoc functionality

		$(document).on('click', '.send-to-hubdoc-btn', function() {

			var $btn = $(this);

			var invoiceId = $btn.data('invoice-id');

			

			// Show loading state

			$btn.html('<i class="fas fa-spinner fa-spin"></i> Sending...');

			$btn.prop('disabled', true);

			

			// Send AJAX request

			$.ajax({

				url: window.ClientDetailConfig.urls.sendToHubdoc + '/' + invoiceId,

				type: 'POST',

				data: {

					_token: window.ClientDetailConfig.csrfToken

				},

				success: function(response) {

					if (response.status) {

						// Show success message

						alert(response.message);

						// Change button text to show it was sent successfully

						$btn.html('<i class="fas fa-check"></i> Sent To Hubdoc');

						$btn.removeClass('btn-primary').addClass('btn-success');

						$btn.prop('disabled', true);

						

						// Add timestamp

						var now = new Date();

						var formattedDate = now.getDate().toString().padStart(2, '0') + '/' + 

										  (now.getMonth() + 1).toString().padStart(2, '0') + '/' + 

										  now.getFullYear() + ' ' +

										  now.getHours().toString().padStart(2, '0') + ':' + 

										  now.getMinutes().toString().padStart(2, '0');

						

						// Remove any existing timestamp

						$btn.siblings('small').remove();

						$btn.after('<br><small style="font-size: 9px; color: #666;">Sent: ' + formattedDate + '</small>');

					} else {

						alert('Error: ' + response.message);

						// Reset button

						$btn.html('<i class="fas fa-paper-plane"></i> Sent To Hubdoc');

						$btn.prop('disabled', false);

					}

				},

				error: function(xhr, status, error) {

					console.error('Error sending to Hubdoc:', error);

					alert('Error sending invoice to Hubdoc. Please try again.');

					// Reset button

					$btn.html('<i class="fas fa-paper-plane"></i> Sent To Hubdoc');

					$btn.prop('disabled', false);

				}

			});

		});



		// Refresh Hubdoc status button

		$(document).on('click', '.refresh-hubdoc-status', function() {

			var $btn = $(this);

			var invoiceId = $btn.data('invoice-id');

			var $mainBtn = $btn.siblings('.send-to-hubdoc-btn');

			

			$btn.html('<i class="fas fa-spinner fa-spin"></i>');

			$btn.prop('disabled', true);

			

			checkHubdocStatus(invoiceId, $mainBtn);

			

			setTimeout(function() {

				$btn.html('<i class="fas fa-sync-alt"></i> Refresh');

				$btn.prop('disabled', false);

			}, 2000);

		});



		// Function to check Hubdoc status

		function checkHubdocStatus(invoiceId, $btn) {

			$.ajax({

				url: window.ClientDetailConfig.urls.checkHubdocStatus + '/' + invoiceId,

				type: 'GET',

				dataType: 'json',

				success: function(response) {

					if (response.hubdoc_sent) {

						// Update button to show sent status

						$btn.html('<i class="fas fa-check"></i> Already Sent At Hubdoc');

						$btn.removeClass('btn-warning').addClass('btn-success');

						

						// Add timestamp if available

						if (response.hubdoc_sent_at) {

							var sentDate = new Date(response.hubdoc_sent_at);

							var formattedDate = sentDate.getDate().toString().padStart(2, '0') + '/' + 

											  (sentDate.getMonth() + 1).toString().padStart(2, '0') + '/' + 

											  sentDate.getFullYear() + ' ' +

											  sentDate.getHours().toString().padStart(2, '0') + ':' + 

											  sentDate.getMinutes().toString().padStart(2, '0');

							

							// Remove any existing timestamp

							$btn.siblings('small').remove();

							$btn.after('<br><small style="font-size: 9px; color: #666;">Sent: ' + formattedDate + '</small>');

						}

					} else {

						// If not sent yet, check again after 3 seconds

						setTimeout(function() {

							checkHubdocStatus(invoiceId, $btn);

						}, 3000);

					}

				},

				error: function(xhr, status, error) {

					console.error('Error checking Hubdoc status:', error);

					//console.log('Response:', xhr.responseText);

					

					// On error, check again after 5 seconds

					setTimeout(function() {

						checkHubdocStatus(invoiceId, $btn);

					}, 5000);

				}

			});

		}



		

		// Edit Date Time functionality

		$(document).on('click', '.editdatetime', function(e){

			e.preventDefault();

			

			var noteId = $(this).attr('data-id');

			$('#edit_note_id').val(noteId);

			

			// Get current note datetime from the page

			var noteCard = $(this).closest('.note-card-redesign');

			var currentDateTime = noteCard.find('.author-updated-date-time').text();

			

			

			// Parse the date format (d/m/Y h:i A) to datetime picker format

			var dateParts = currentDateTime.split(' ');

			var datePart = dateParts[0].split('/');

			var timePart = dateParts[1];

			var ampm = dateParts[2];

			

			var day = datePart[0];

			var month = datePart[1];

			var year = datePart[2];

			var time = timePart.split(':');

			var hours = parseInt(time[0]);

			var minutes = time[1];

			

			// Convert to 24-hour format

			if (ampm === 'PM' && hours !== 12) {

				hours += 12;

			} else if (ampm === 'AM' && hours === 12) {

				hours = 0;

			}

			

			var formattedDate = year + '-' + 

							   String(month).padStart(2, '0') + '-' + 

							   String(day).padStart(2, '0') + ' ' + 

							   String(hours).padStart(2, '0') + ':' + 

							   String(minutes).padStart(2, '0');

			

			$('#edit_datetime').val(formattedDate);

			$('#edit_datetime_modal').modal('show');

		});





        // Edit Date Time functionality

		$('.editdatetime').off('click').on('click', function(e) { 

			e.preventDefault();

			

			var noteId = $(this).attr('data-id');

			$('#edit_note_id').val(noteId);

			

			// Get current note datetime from the page

			var noteCard = $(this).closest('.note-card-redesign');

			var currentDateTime = noteCard.find('.author-updated-date-time').text();

			

			

			// Parse the date format (d/m/Y h:i A) to datetime picker format

			var dateParts = currentDateTime.split(' ');

			var datePart = dateParts[0].split('/');

			var timePart = dateParts[1];

			var ampm = dateParts[2];

			

			var day = datePart[0];

			var month = datePart[1];

			var year = datePart[2];

			var time = timePart.split(':');

			var hours = parseInt(time[0]);

			var minutes = time[1];

			

			// Convert to 24-hour format

			if (ampm === 'PM' && hours !== 12) {

				hours += 12;

			} else if (ampm === 'AM' && hours === 12) {

				hours = 0;

			}

			

			var formattedDate = year + '-' + 

							   String(month).padStart(2, '0') + '-' + 

							   String(day).padStart(2, '0') + ' ' + 

							   String(hours).padStart(2, '0') + ':' + 

							   String(minutes).padStart(2, '0');

			

			$('#edit_datetime').val(formattedDate);

			$('#edit_datetime_modal').modal('show');

		});

		

		

		// Initialize datetime picker when modal is shown

		$('#edit_datetime_modal').on('shown.bs.modal', function() {

			$('#edit_datetime').datetimepicker({

				format: 'Y-m-d H:i',

				step: 15,

				inline: false,

				theme: 'default'

			});

		});

		

		// Save datetime functionality

		$(document).on('click', '#save_datetime_btn', function(e){

			var noteId = $('#edit_note_id').val();

			var newDateTime = $('#edit_datetime').val();

			

			if (!newDateTime) {

				alert('Please select a date and time');

				return;

			}

			

			$.ajax({

				url: window.ClientDetailConfig.urls.updateNoteDatetime,

				type: 'POST',

				data: {

					_token: window.ClientDetailConfig.csrfToken,

					note_id: noteId,

					datetime: newDateTime

				},

				success: function(response) {

					if (response.status) {

                        alert('Date and time updated successfully!');

                        // Update the displayed datetime on the page

						var noteCard = $('.note-card-redesign[data-id="' + noteId + '"]');

						var dateObj = new Date(newDateTime);

						var hours = dateObj.getHours();

						var ampm = hours >= 12 ? 'PM' : 'AM';

						var displayHours = hours % 12;

						if (displayHours === 0) displayHours = 12;

						

						var formattedDate = dateObj.getDate().toString().padStart(2, '0') + '/' + 

										  (dateObj.getMonth() + 1).toString().padStart(2, '0') + '/' + 

										  dateObj.getFullYear() + ' ' +

										  displayHours.toString().padStart(2, '0') + ':' + 

										  dateObj.getMinutes().toString().padStart(2, '0') + ' ' + ampm;

						noteCard.find('.author-updated-date-time').text(formattedDate);

						$('#edit_datetime_modal').modal('hide');

						

					} else {

						alert('Error: ' + response.message);

					}

				},

				error: function(xhr, status, error) {

					console.error('Error updating datetime:', error);

					alert('Error updating date and time. Please try again.');

				}

			});

		});



		// Email Handling JavaScript moved to separate file



		// Client Portal Toggle Functionality

		$('#client-portal-toggle').on('change', function() {

			var isChecked = $(this).is(':checked');

			var clientId = $(this).data('client-id');

			var toggleElement = $(this);



			// Disable toggle during request

			toggleElement.prop('disabled', true);



			$.ajax({

				url: window.ClientDetailConfig.urls.toggleClientPortal,

				method: 'POST',

				data: {

					client_id: clientId,

					status: isChecked,

					_token: window.ClientDetailConfig.csrfToken

				},

				success: function(response) {

					if (response.success) {

						// Show success message

						alert('Success: ' + response.message);

					} else {

						// Revert toggle state on error

						toggleElement.prop('checked', !isChecked);

						alert('Error: ' + (response.message || 'Error updating client portal status'));

					}

				},

				error: function(xhr, status, error) {

					// Revert toggle state on error

					toggleElement.prop('checked', !isChecked);

					

					var errorMessage = 'Error updating client portal status';

					if (xhr.responseJSON && xhr.responseJSON.message) {

						errorMessage = xhr.responseJSON.message;

					}

					

					alert('Error: ' + errorMessage);

					console.error('Client Portal Toggle Error:', error);

				},

				complete: function() {

					// Re-enable toggle

					toggleElement.prop('disabled', false);

				}

			});

		});

	});




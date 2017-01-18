$(document).ready(function (){

    /**
     * Focus on first filter's third field on page load
     */
    $("div.filters").find('.thirdFilter').eq(0).find("input, select").focus();

    /**
     * Save/Saving button label switcher
     */

    $("body").delegate("button.submit", "click", function(){
        $(this).find("span").hide();
        $(this).find("span").eq(1).show();
    });

	initSortableTable();
    processBasedOnSecondFilter();
    resetRowIndexes();
    toggleRemoveFilterRuleDisplay();

    /**
     * Run a query on page load if
     * filter data is available
     */
    if( $("div.filters").hasClass('has-cached-filter-data') )
    {
        runQuery($("div.filters"));
    }

    /**
     * Prevent the filter form from submitting on
     * pressing Enter key
     */
    $("form#zenbuSearchFilter").submit(function(e){
        e.preventDefault();
    });


    /**
     * Adding a collapsible sidebar
     */
    $("div.content").addClass('hiding-sidebar');
    $("div.sidebar").hide();

    $("body").delegate("a.show-sidebar-button", "click", function(){
        if($(this).hasClass("showing"))
        {
            $("div.content").addClass('hiding-sidebar');
            $("div.sidebar").hide();
            $(this).removeClass("showing").show();
            $(this).find("i.fa-expand").removeClass("fa-caret-left").addClass("fa-caret-right");
        }
        else
        {
            $("div.content").removeClass('hiding-sidebar');
            $("div.sidebar").show();
            $(this).addClass("showing").show();
            $(this).find("i.fa-expand").removeClass("fa-caret-right").addClass("fa-caret-left");
        }
    });


    $(".entryType_select").hide().find("select").attr('disabled', 'disabled');

    /**
     * Display entryType dropdown if section has
     * more than one entry type
     */
    var sectionId = $("div.filters select#section_select").val();
    $(".entryType_select").hide().find("select").attr('disabled', 'disabled');
    $(".sectionId_"+sectionId).show().find("select").removeAttr('disabled');

    $("div.filters select#section_select").change(function (e) {
        var sectionId = $(this).val() !== '' ? $(this).val() : 0;
        $(".entryType_select").hide().find("select").attr('disabled', 'disabled');
        $(".sectionId_"+sectionId).show().find("select").removeAttr('disabled');
        fetchNewEntryButton(sectionId);
    });


    /**
     * Run the query, filters, etc, based on select field change
     */
    $("body").delegate("div.filters select", "change", function (e) {

        e.preventDefault();

        if($(this).attr('name') == 'sectionId' || $(this).attr('name') == 'entryTypeId')
        {
            var sectionId = $("div.filters select#section_select").val() !== '' ? $("div.filters select#section_select").val() : 0;
            var entryTypeId = $("div.filters div.entryType_select.sectionId_"+sectionId).find("select").val();
            updateTabUrls();
            fetchFirstFilters(sectionId, entryTypeId);
        }

        if($(this).closest("td").hasClass('firstFilter'))
        {
            fetchSecondFilters();
            fetchThirdFilters();
        }

        processBasedOnSecondFilter();
        resetRowIndexes();
        runQuery($(this));
    });

    /**
    *   Adding typeWatch delay when typing.
    *   Less calls == less sluggishness
    */
    var typeWatchOptions = {
        callback: function () {
            runQuery($("div.filters"));
        },
        wait: 750,
        highlight: false,
        captureLength: 0
    }

    /**
     * Bind search after typing delay
     */
    $(".thirdFilter input").typeWatch(typeWatchOptions);

    $("body").delegate("a.pagination", "click", function (e) {
        e.preventDefault();
        runQuery($("div.filters"), $(this).attr('href'));
    });

    /**
     * Datepicker
     */
    $("body").delegate(".datepicker input", "focusin", function(e) {
        var datepickerElem = $(this);
        $(this).datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function() {
                runQuery(datepickerElem);
            }
        });
    });


    /**
     * Add filterRule Rows
     */

    $("body").delegate(".addFilterRule", "click", function (e) {
        filterRow = $(this).closest("tr.filter-params").clone();
        filterRow.find(".thirdFilter select, .thirdFilter input").val('');
        $("tr.filter-params").last().after(filterRow);
        fetchSecondFilters();
        fetchThirdFilters();
        processBasedOnSecondFilter();
        resetRowIndexes();
        toggleRemoveFilterRuleDisplay();
    });


    /**
     * Remove filterRule Row
     */
    $("body").delegate(".removeFilterRule", "click", function (e) {
        if($("tr.filter-params").length > 1)
        {
            $(this).closest("tr.filter-params").remove();
            fetchSecondFilters();
            fetchThirdFilters();
            processBasedOnSecondFilter();
            resetRowIndexes();
            runQuery($("div.filters"));
        }

        toggleRemoveFilterRuleDisplay();
    });


    /**
     * Select All
     */
    $("body").delegate("[name=selectAll]", "click", function(e){
        var checked  = $(this).is(':checked');
        if(checked === true)
        {
            $("[name*=elementIds]").prop("checked", true);
        }
        else
        {
            $("[name*=elementIds]").prop("checked", false);
        }
    });


    /**
     * Main - Show action button when entries are selected
     */
    $("body").delegate("[name*=elementIds], [name=selectAll]", "click", function(e){
        var totalSelected = $("[name*=elementIds]:checked").length;
        if(totalSelected >= 1)
        {
            $(".actionDisplay").show();
        }
        else
        {
            $(".actionDisplay").hide();
        }
    });


    /**
     * Saved Searches - Show action button when saved searches are selected
     */
    $("body").delegate("[name*=searchIds]", "click", function(e){
        var totalSelected = $("[name*=searchIds]:checked").length;
        if(totalSelected >= 1)
        {
            $(".savedSearchActions").show();
        }
        else
        {
            $(".savedSearchActions").hide();
        }
    });


    /**
     * Modal window
     */
    $("body").delegate(".showModal", "click", function(e){
        e.preventDefault();
        var $div = $(this).next("div.outerModal").html();
        var myModal = new Garnish.Modal($div, {
            resizable: true,
            onHide: function(){
                myModal.destroy();
                $(".modal-shade").remove();
            }
        });
    });


    /**
     * Image Modal window
     */
    $("body").delegate(".imageModal", "click", function(e){
        e.preventDefault();
        var imageUrl = $(this).attr("href");
        var image = $(this).next("div.outerModal").find("div.modal").html('<iframe src="'+imageUrl+'" class="imageiframe"></iframe>');
        var $div = $(this).next("div.outerModal").html();
        var originalWidth = $(this).attr("data-original-width");
        var originalHeight = $(this).attr("data-original-height");
        var modalWindow = Garnish.Modal.extend({
           desiredWidth: originalWidth,
           desiredHeight: originalHeight,
        });

        var myModal = new modalWindow($div, {
            resizable: true,
            onShow: function(){
                // Garnish's Modal doesn't go lower than ~600px in width.
                // This means even smaller images have wasted whitespace.
                // The following readjusts the modal to be smaller and centered
                $("div.modal").css({
                        'width':        originalWidth,
                        'min-width':    originalWidth + 'px',
                        'height':       originalHeight,
                        'min-height':   originalHeight + 'px',
                        'left':         Math.round((Garnish.$win.width() - originalWidth) / 2)
                    });
            },
            onHide: function(){
                myModal.destroy();
                $(".modal-shade").remove();
            }
        });
    });


    /**
     * Fetch "New Entry" button
     */
    function fetchNewEntryButton(sectionId)
    {
        newEntryButton = $("div.filterFields").find("div.new-entry-sectionId-" + sectionId).clone();
        $(".new-entry").empty().html(newEntryButton);

        // Activate the dropdown menu for "New Entry" 
        // after selecting "All Sections"
        if(sectionId === 0)
        {
            newEntryButton.find("div.menu-dummy").removeClass("menu-dummy").addClass("menu");
            newEntryButton.find("div.savedSearchActions").menubtn();
        }
    }


    /**
     * Update Tab URLs
     */
    function updateTabUrls()
    {
        var sectionId = $("div.filters select#section_select").val();
        var entryTypeId = $("div.filters div.entryType_select.sectionId_"+sectionId).find("select").val();
        $("nav").find("a.tab").each(function(){
            rawUrl           = $(this).attr('href');
            originalUrl      = rawUrl.replace(/\?(.*)/g, '');

            // Modify only Tab Urls that use GET variables
            if(rawUrl.indexOf("searches") == -1)
            {
                sectionIdParam   = typeof sectionId !== 'undefined' && sectionId !== '' ? '?sectionId=' + sectionId : '';
                entryTypeIdParam = typeof entryTypeId !== 'undefined' && entryTypeId !== '' ? '&entryTypeId=' + entryTypeId : '';
                $(this).attr('href', originalUrl  + sectionIdParam + entryTypeIdParam);
            }
        });
    }


    /**
     * Show or don't show "-" remove
     * filter rule button
     */
    function toggleRemoveFilterRuleDisplay()
    {
        if($("tr.filter-params").length > 1)
        {
            $(".removeFilterRule").show();
        }
        else
        {
            $(".removeFilterRule").hide();
        }
    }


    /**
     * Fetch the 1st filters corresponding to
     * the selected Section and entryType
     */
    function fetchFirstFilters(sectionId, entryTypeId){

        // Loop through each filter row
        $("tr.filter-params").each(function(){
            // Get original value of the 1st filter in row
            originalFirstFieldValue = $(this).find("td.firstFilter").find("select").val();

            // Clone 1st filter
            // Fetch and clone field from hidden filterFields
            dropdownClass = typeof entryTypeId !== 'undefined' ? '.sectionId_' + sectionId + '.entryTypeId_' + entryTypeId : '.sectionId_' + sectionId;
            firstFilter = $("div.filterFields").find("td.firstFilter" + dropdownClass).clone();

            // Set the cloned field to the original value
            firstFilter.find("select").val(originalFirstFieldValue);

            // Set the cloned field to the first option if 
            // the original value does not match something 
            // in the new cloned field
            if( ! firstFilter.find("select option:selected").length)
            {
                firstFilter.find("select").val(firstFilter.find("select option:first").val());
            }

            fetchSecondFilters();

            // Replace original field with cloned field
            $(this).find("td.firstFilter").replaceWith(firstFilter);
        });
    }


    /**
     * Fetch the 2nd filters corresponding to
     * the selected 1st filter
     */
    function fetchSecondFilters()
    {
        $("tr.filter-params").each(function(){

            // Get original value of the 1st filter in row
            originalFirstFieldValue = $(this).find("td.firstFilter").find("select").val();
            originalSecondFieldValue = $(this).find("td.secondFilter").find("select").val();

            // Clone 2nd filter
            if($.inArray(originalFirstFieldValue, ['status']) !== -1)
            {
                secondFilter = $("div.filterFields").find("td.secondFilter.index_0").clone();
            }
            else if($.inArray(originalFirstFieldValue, ['id']) !== -1)
            {
                secondFilter = $("div.filterFields").find("td.secondFilter.index_1").clone();
            }
            else if($.inArray(originalFirstFieldValue, ['postDate']) !== -1)
            {
                secondFilter = $("div.filterFields").find("td.secondFilter.index_2").clone();
            }
            else if($.inArray(originalFirstFieldValue, ['title', 'uri', 'author']) !== -1)
            {
                secondFilter = $("div.filterFields").find("td.secondFilter.index_3").clone();
            }
            else
            {
                secondFilter = $("div.filterFields").find("td.secondFilter.index_4").clone();
            }

            // Set the cloned field to the original value
            secondFilter.find("select").val(originalSecondFieldValue);

            // Set the cloned field to the first option if 
            // the original value does not match something 
            // in the new cloned field
            if( ! secondFilter.find("select option:selected").length)
            {
                secondFilter.find("select").val(secondFilter.find("select option:first").val());
            }

            // Replace original field with cloned field
            $(this).find("td.secondFilter").replaceWith(secondFilter);
        });
    }


    /**
     * Some processing to run based on second filter value
     */
    function processBasedOnSecondFilter()
    {
        $("tr.filter-params").each(function(){
            originalSecondFieldValue = $(this).find("td.secondFilter").find("select").val();
            originalThirdField = $(this).find(".thirdFilter select, .thirdFilter input, input.thirdFilter, select.thirdFilter");

            if($.inArray(originalSecondFieldValue, ['isempty', 'isnotempty']) !== -1)
            {
                originalThirdField.hide();
            }
            else
            {
                originalThirdField.show();
            }
        });
    }


    /**
     * Fetch the 3nd filters corresponding to
     * the selected 1st filter
     */
    function fetchThirdFilters()
    {
        $("tr.filter-params").each(function(){
            // Get original value of the 1st filter in row
            originalFirstFieldValue = $(this).find(".firstFilter").find("select").val();
            originalSecondFieldValue = $(this).find("td.secondFilter").find("select").val();
            originalThirdFieldValue = $(this).find(".thirdFilter select, .thirdFilter input, input.thirdFilter, select.thirdFilter").val();

            // Clone 3rd filter
            if($.inArray(originalFirstFieldValue, ['status']) !== -1)
            {
                thirdFilter = $("div.filterFields").find(".thirdFilter.statusSelect").clone();
            }
            else if($.inArray(originalFirstFieldValue, ['postDate']) !== -1)
            {
                thirdFilter = $("div.filterFields").find(".thirdFilter.datepicker").clone();
                originalThirdFieldValue = '';
            }
            else
            {
                thirdFilter = $("div.filterFields").find(".thirdFilter.generalInput").clone();
            }



            // Set the cloned field to the original value
            thirdFilter.val(originalThirdFieldValue).find("select, input").val(originalThirdFieldValue);

            // Replace original field with cloned field
            $(this).find(".thirdFilter").replaceWith(thirdFilter);
        });

        $(".thirdFilter input").typeWatch(typeWatchOptions);
    }


    /**
     * Reset filter indexes, i.e. order X in "filter[X]"
     */
    function resetRowIndexes()
    {
        $("tr.filter-params").each(function(index){
            rowIndex = index;
            $(this).find('select, input').each(function(){
                oldName = $(this).attr('name');
                newName = oldName.replace(/filter\[\d\]/g, 'filter['+ rowIndex + ']');
                $(this).attr('name', newName);
            });
        });

        $("table.settingsTable tbody").find("td.order").each(function(index){
           var rowIndex = index + 1;
           $(this).html(rowIndex);

           $(this).next("td").find("input[type=hidden]").each(function(){

            oldName = $(this).attr('name');
            newName = oldName.replace(/field\[\d\]/g, 'field['+ rowIndex + ']');
            $(this).attr('name', newName);

           });
        });
    }


    /**
     * Run the main query
     */
    function runQuery(elem, theURL)
    {
        var theAction = typeof theURL !== 'undefined' ? theURL : elem.closest("form").attr("action");
        var theData = elem.closest("form").serialize() + '&' + csrfTokenName + '=' + csrfTokenValue;
        $(".loading").show();

        $.ajax({
                    type:     "POST",
                    url: theAction, // <= Providing the URL
                    data: theData, // <= Providing the form data, serialized above
                    success: function(results){
                            // What to do when the ajax is successful.
                            // "results" is the response from the url (eg. "theAction" here)
                            $("div.resultArea").html(results);
                            $('.lightswitch').lightswitch();
                            initSortableTable();
                            $(".loading").hide();
                            resetRowIndexes();
                        },
                    error: function(results){
                            // What to do when the ajax fails.
                            console.log(results.statusText);
                            console.log(results.responseText);
                            $("div.resultArea").html(results.responseText);
                            $(".loading").hide();
                    }
        });
    }


    /**
     * Start sortable table
     */
    function initSortableTable()
    {
        /**
        *   ==============================
        *   Sortable
        *   ==============================
        */

        /**
        *  Make table rows sortable!
        *  Return a helper with preserved width of cells
        */
        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };

        $("table.settingsTable tbody").sortable({
            cancel: 'table.settingsTable tr th, .not-sortable',
            start: function(){
            },
            stop: function(event, ui) {
                resetRowIndexes();
            },
            placeholder: 'ui-state-highlight',
            forcePlaceholderSize: true,
            helper: fixHelper,
            revert: 200,
            cursor: 'move',
            distance: 15
        });
    }


    /**
     * Confirmation box
     */
    $("body").delegate("a.action", "click", function(e){
        e.preventDefault();

        var param          = $(this).attr('data-param');
        var value          = $(this).attr('data-value');
        var confirmMessage = $(this).attr('data-confirm');
        var returnUrl      = $(this).attr('data-returnUrl');

        if( typeof confirmMessage !== 'undefined' )
        {
            var answer = confirm( confirmMessage );

            if(answer)
            {
                var theAction = $("form#resultList").attr('action');
                var theData   = $("form#resultList").serialize() + '&' + param + '=' + value;

                $.ajax({
                    type:     "POST",
                    url: theAction,
                    data: theData,
                    //dataType: 'json',
                    success: function(results){
                             runQuery($("div.filters"));
                             if($("div.actionDisplay").hasClass('hideAfterAction'))
                             {
                                $("div.actionDisplay").hide();
                             }

                             if( typeof returnUrl !== 'undefined' )
                             {
                                window.location = returnUrl;
                             }
                        },
                    error: function(results){
                            console.log(results.statusText);
                            console.log(results.responseText);
                    }
                });
            } else {
                return false;
            }
        }
    });


    /**
     * Saving a Search
     */
    $("body").delegate("form#saveSearch", "submit", function(e){
        e.preventDefault();

        if($(this).find("input[name=label]").val() === '')
        {
            $("button.submit").find("span").hide().eq(0).show();
            return;
        }

        var theAction = $(this).attr("action");
        var theData = $(this).serialize();
        var theFilterData = $("form#zenbuSearchFilter").serialize();
        theData = theData + '&' + theFilterData;

        $(".loading").show();

        $.ajax({
                    type:     "POST",
                    url: theAction,
                    data: theData,
                    //dataType: 'json',
                    success: function(results){
                            $("ul#savedSearchesList").append('<li>' + results.link + '</li>');
                            $(".loading").hide();
                            $("button.submit").find("span").hide().eq(0).show();
                            $("form#saveSearch").find("input[name=label]").val('');
                            $("li.heading.savedSearches").show();
                        },
                    error: function(results){
                            console.log(results.statusText);
                            console.log(results.responseText);
                            $(".loading").hide();
                    }
        });
    });


    /**
     * Saved Search Loading
     */
    $("body").delegate("ul#savedSearchesList li a", "click", function(e){

        e.preventDefault();

        var searchId = $(this).attr('data-searchId');
        var theAction = $(this).attr("href");

        $.ajax({
                type:       "POST",
                url:        theAction,
                data:       'searchId=' + searchId + '&' + csrfTokenName + '=' + csrfTokenValue,
                dataType:   'json',
                success: function(results){

                    var sectionId = 0;
                    var entryTypeId = 0;

                    // Find sectionId and entryTypeId
                    $.each(results, function(item, filter)
                    {
                        if(filter.filterAttribute1 == 'sectionId')
                        {
                            sectionId = filter.filterAttribute3;
                        }

                        if(filter.filterAttribute1 == 'entryTypeId')
                        {
                            entryTypeId = filter.filterAttribute3;
                        }
                    });

                    // Get the total filters
                    var totalFilters = 0;
                    $.each(results, function(item, filter){
                        if( $.inArray(filter.filterAttribute1, ['sectionId', 'entryTypeId', 'orderby', 'limit']) === -1 )
                        {
                            totalFilters++;
                        }
                    });

                    // Keep the first filterRow and remove others
                    $("tr.filter-params").not(':first').remove();

                    // Clone the kept filterRow as many times as there are filters
                    for(i = 1; i < totalFilters; i++)
                    {
                        firstFilterRow = $("tr.filter-params").eq(0).clone();
                        $("tr.filter-params").last().after(firstFilterRow);
                    }

                    // Fetch dropdowns based on section/entrytype
                    if(typeof sectionId !== 'undefined' && typeof entryTypeId !== 'undefined')
                    {
                        var entryTypeIdParam;

                        if(entryTypeId !== 0)
                        {
                            entryTypeIdParam = entryTypeId;
                        }

                        fetchFirstFilters(sectionId, entryTypeIdParam);
                        fetchSecondFilters();
                        fetchThirdFilters();
                        processBasedOnSecondFilter();
                        resetRowIndexes();
                    }

                    // Set values in fields
                    var rowIndex = 0;
                    $.each(results, function(item, filter){

                        if(filter.filterAttribute1 == 'sectionId')
                        {
                            sectionIdVal = sectionId === '0' ? '' : sectionId;
                            $("div.filters select#section_select").val(sectionIdVal);
                            $(".entryType_select").hide().find("select").attr('disabled', 'disabled');
                            $(".entryType_select.sectionId_"+sectionId).show().find("select").removeAttr('disabled').val(entryTypeId);
                        }

                        if( $.inArray(filter.filterAttribute1, ['sectionId', 'entryTypeId', 'orderby', 'limit']) === -1 )
                        {
                            $("tr.filter-params").eq(rowIndex).find(".firstFilter select").val(filter.filterAttribute1);
                            fetchSecondFilters();
                            $("tr.filter-params").eq(rowIndex).find(".secondFilter select").val(filter.filterAttribute2);
                            fetchThirdFilters();
                            $("tr.filter-params").eq(rowIndex).find(".thirdFilter select, .thirdFilter input").val(filter.filterAttribute3);
                            $("tr.filter-params").eq(rowIndex).find(".thirdFilter").val(filter.filterAttribute3);
                            rowIndex++;
                        }

                        if(filter.filterAttribute1 == 'limit')
                        {
                            $("div.filters select[name=limit]").val(filter.filterAttribute2);
                        }

                        if(filter.filterAttribute1 == 'orderby')
                        {
                            $("div.filters select[name=orderby]").val(filter.filterAttribute2);
                            $("div.filters select[name=sort]").val(filter.filterAttribute3);
                        }

                    });
                    
                    $(".loading").show();
                    processBasedOnSecondFilter();
                    resetRowIndexes();
                    runQuery($("div.filters"));
                    updateTabUrls();
                    toggleRemoveFilterRuleDisplay();

                },
                error: function(results){
                        console.log(results.statusText);
                        console.log(results.responseText);
                        $(".loading").hide();
                }
        });
    });

});
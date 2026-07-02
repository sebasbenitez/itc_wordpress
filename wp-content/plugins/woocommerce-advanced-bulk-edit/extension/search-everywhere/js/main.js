window.addEventListener('load', function () {
    var btnSearch = document.getElementById('btn-search-everywhere');
    var textSearch = document.getElementById('text-search-everywhere');
    var checkIgnoreCase = document.getElementById('check-search-everywhere-ignore-case');
    if (!btnSearch) {
        return;
    }

    function searchString(haystack, needle) {
        if (checkIgnoreCase.checked) {
            return haystack.toLowerCase().includes(needle.toLowerCase());
        } else {
            return haystack.includes(needle);
        }
    }

    function searchObject(obj, needle) {
        for (let prop in obj) {
            if (obj[prop] !== undefined &&
                obj[prop].value !== undefined &&
                typeof obj[prop].value === 'string' &&
                searchString(obj[prop].value, needle)) {
                
                return true;
            }
        }
        return false;
    }

    btnSearch.addEventListener('click', function () {
        var searchPhrase = textSearch.value;
        if (searchPhrase.trim().length === 0) {
            return;
        }
        if (W3Ex.abemodule.getGridItem().getData().length > 0) {
            var selected_rows = [];
            W3Ex.abemodule.getGridItem().getData().forEach(function(row, i) {
                Object.keys(row).forEach(function(cell) {
                    var resultFound = false;
                    if (typeof row[cell] === 'string') {
                        resultFound = searchString(row[cell], searchPhrase);
                    } else if (typeof row[cell] === 'object') {
                        resultFound = searchObject(row[cell], searchPhrase);
                    }
                    if (resultFound && selected_rows.indexOf(i) === -1) {
                        selected_rows.push(i);
                    }
                });
            });
            W3Ex.abemodule.getGridItem().setSelectedRows(selected_rows);
            $("#selectdialog").dialog('close');
        }
    })
});

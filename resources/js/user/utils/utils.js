
/**
 * Unshifts a new value into a paginated ref array, updates totals, 
 * enforces the row limit, and resets pagination to the first page.
 * * @param {Ref<Array>} arr - The ref holding your table data (e.g., phoneNumbers)
 * @param {Object} value - The new item to add
 * @param {Ref<Number>} totalRecords - The ref holding the total number of records
 * @param {Ref<Number>} first - The ref holding the pagination offset
 * @param {Ref<Number>|Number} rows - The variable holding the max rows per page (e.g., 10, 20)
 */
export function unshiftToRefArray(arr, value, totalRecords, first, rows) {
    arr.value.unshift(value);
    
    arr.value = [...arr.value];

    if (totalRecords) {
        totalRecords.value++;
    }


    if (arr.value.length > rows) {
        arr.value.pop();
    }

    if (first && first.value !== 0) {
        first.value = 0;
    }
}
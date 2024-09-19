
const DEF_SHOW_ROWS_NUM = 10;
const DEF_SORT_BY_FIELD_ID = 0;
const DEF_SORT_DIRECTION = "asc";
const DEF_SHOW_PAGINATION = true;
const MAX_PAGE_TO_SHOW = 3;

export default class VanillaDataTable {
    constructor(element, options = {}) {
        this.element = element;
        this.fields = [];
        this.data = [];
        this.sorted_data = [];
        this.current_start = 0;
        this.current_page = 0;

        this.options = {
            show_rows_num: options.show_rows_num || DEF_SHOW_ROWS_NUM,
            sort_by_field_id: options.sort_by_field_id || DEF_SORT_BY_FIELD_ID,
            sort_direction: options.sort_direction || DEF_SORT_DIRECTION,
            show_pagination: typeof options.show_pagination === "boolean" ? options.show_pagination : DEF_SHOW_PAGINATION,
        };

        this.current_sort_field_id = this.options.sort_by_field_id;
        this.current_sort_direction = this.options.sort_direction;
        this.current_show_rows_num = this.options.show_rows_num;
    }

    init = () => {
        this.element.classList.add("vdt");

        this.fields = this.#getFields();
        this.data = this.#getData();
        this.sorted_data = this.#sortData(this.options.sort_by_field_id, this.options.sort_direction);

        this.#render(0, this.options.show_rows_num);

        if (this.options.show_pagination) {
            this.#renderPagination();
        }
    };

    #getFields = () => {
        let fields = [];

        this.element.querySelectorAll("thead > tr > th").forEach((e, i) => {
            let name = e.innerText;
            e.innerText = "";

            let name_btn = document.createElement("button");
            name_btn.classList.add("vdt-name");
            name_btn.innerText = name;
            e.appendChild(name_btn);

            name_btn.addEventListener("click", this.#onFieldNameClick(i));

            let arrows_div = document.createElement("div");
            arrows_div.classList.add("vdt-arrows");
            arrows_div.innerText = ""; //"▲▼";
            e.appendChild(arrows_div);

            let arrow_up = document.createElement("span");
            arrow_up.classList.add("vdt-up");
            arrow_up.innerText = "▲";
            arrows_div.appendChild(arrow_up);

            let br = document.createElement("br");
            arrows_div.appendChild(br);

            let arrow_down = document.createElement("span");
            arrow_down.classList.add("vdt-down");
            arrow_down.innerText = "▼";
            arrows_div.appendChild(arrow_down);

            fields.push({
                name: name,
                element: e
            });
        });

        return fields;
    };

    #getData = () => {
        let data = [];

        this.element.querySelectorAll("tbody > tr").forEach(e => {
            let row = {
                row_element: e
            };

            e.querySelectorAll("td").forEach((f, i) => {
                row[this.fields[i].name] = {
                    data: f.innerText,
                    element: f
                };
            });

            data.push(row);
        });

        return data;
    };

    #sortData = (field_id, direction) => {
        let sorted_data = this.data.sort((a, b) => {
            if (a[this.fields[field_id].name].data < b[this.fields[field_id].name].data) {
                return direction === "asc" ? -1 : 1;
            } else if (a[this.fields[field_id].name].data > b[this.fields[field_id].name].data) {
                return direction === "asc" ? 1 : -1;
            } else {
                return 0;
            }
        });

        return sorted_data;
    };

    #renderPagination = () => {
        let num_results = this.data.length;
        let num_pages = Math.ceil(num_results / this.options.show_rows_num);
        //let pages_max_offset = Math.floor(MAX_PAGE_TO_SHOW / 2)

        let old_pagination = this.element.nextElementSibling;
        if (old_pagination && old_pagination.classList.contains("vdt-pagination")) {
            old_pagination.remove();
        }

        let pagination = document.createElement("div");
        pagination.classList.add("vdt-pagination");
        pagination.innerHTML = "";

        this.element.insertAdjacentElement("afterend", pagination);

        let first_btn = document.createElement("button");
        first_btn.classList.add("vdt-first");
        first_btn.innerText = "«";
        first_btn.addEventListener("click", this.#onPaginationClick(0));
        if (this.current_page === 0) {
            first_btn.disabled = true;
        }

        pagination.appendChild(first_btn);

        let prev_btn = document.createElement("button");
        prev_btn.classList.add("vdt-prev");
        prev_btn.innerText = "‹";
        prev_btn.addEventListener("click", this.#onPaginationClick(this.current_page - 1));
        pagination.appendChild(prev_btn);
        if (this.current_page === 0) {
            prev_btn.disabled = true;
        }

        for (let i = 0; i < num_pages; i++) {
            // if (this.current_page - i > pages_max_offset) {
            // }

            let page_btn = document.createElement("button");
            page_btn.innerText = i + 1;
            page_btn.addEventListener("click", this.#onPaginationClick(i));
            pagination.appendChild(page_btn);

            if (i === this.current_page) {
                page_btn.classList.add("vdt-current");
            } else {
                page_btn.classList.remove("vdt-current");
            }
        }

        let next_btn = document.createElement("button");
        next_btn.classList.add("vdt-next");
        next_btn.innerText = "›";
        next_btn.addEventListener("click", this.#onPaginationClick(this.current_page + 1));
        if (this.current_page === num_pages - 1) {
            next_btn.disabled = true;
        }

        pagination.appendChild(next_btn);

        let last_btn = document.createElement("button");
        last_btn.classList.add("vdt-last");
        last_btn.innerText = "»";
        last_btn.addEventListener("click", this.#onPaginationClick(num_pages - 1));
        if (this.current_page === num_pages - 1) {
            last_btn.disabled = true;
        }

        pagination.appendChild(last_btn);
    };

    #render = (start, qty) => {
        this.current_start = start;

        this.fields.forEach((e, i) => {
            e.element.classList.remove("vdt-asc", "vdt-desc");

            if (i === this.current_sort_field_id) {
                e.element.classList.add("vdt-" + this.current_sort_direction);
            }
        });

        this.sorted_data.forEach((e, i) => {
            if (i >= start && i < start + qty) {
                this.element.appendChild(e.row_element);
            } else {
                e.row_element.remove();
            }
        });
    };

    #onFieldNameClick = (field_id) => {
        return (e) => {
            if (this.current_sort_field_id === field_id && this.current_sort_direction === "asc") {
                this.current_sort_direction = "desc";
            } else if (this.current_sort_field_id === field_id && this.current_sort_direction === "desc") {
                this.current_sort_field_id = this.options.sort_by_field_id;
                this.current_sort_direction = this.options.sort_direction;
            } else {
                this.current_sort_field_id = field_id;
                this.current_sort_direction = "asc";
            }

            this.sorted_data = this.#sortData(this.current_sort_field_id, this.current_sort_direction);
            this.#render(this.current_start, this.current_show_rows_num);
        }
    };

    #onPaginationClick = (page) => {
        return (e) => {
            this.current_start = page * this.current_show_rows_num;
            this.current_page = page;
            this.#render(this.current_start, this.current_show_rows_num);
            this.#renderPagination();
        }
    };
}
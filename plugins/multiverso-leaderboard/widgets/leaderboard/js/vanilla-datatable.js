
const DEF_SHOW_ROWS_NUM = 10;
const DEF_SORT_BY_FIELD_ID = 0;
const DEF_SORT_DIRECTION = "asc";
const DEF_SHOW_PAGINATION = true;
const DEF_SHOW_FILTER = true;
const MAX_PAGE_TO_SHOW = 3;
const TIME_FIELD_PATTERN = /(\d+) minuti e (\d+) secondi/;

export default class VanillaDataTable {
    constructor(element, options = {}) {
        this.element = element;
        this.fields = [];
        this.data = [];
        this.sorted_data = [];
        this.filtered_data = [];
        this.current_start = 0;
        this.current_page = 0;

        this.options = {
            show_rows_num: options.show_rows_num || DEF_SHOW_ROWS_NUM,
            sort_by_field_ids: options.sort_by_field_ids || [DEF_SORT_BY_FIELD_ID],
            sort_directions: options.sort_directions || [DEF_SORT_DIRECTION],
            show_pagination: typeof options.show_pagination === "boolean" ? options.show_pagination : DEF_SHOW_PAGINATION,
            show_filter: typeof options.show_filter === "boolean" ? options.show_filter : DEF_SHOW_FILTER,
        };

        this.current_sort_field_ids = [...this.options.sort_by_field_ids];
        this.current_sort_directions = [...this.options.sort_directions];
        this.current_show_rows_num = this.options.show_rows_num;
    }

    init = () => {
        this.element.classList.add("vdt");

        this.fields = this.#getFields();

        const def_order = this.#getDefaultOrderByFields();
        this.options.sort_by_field_ids = def_order.order_by_fields_id;
        this.options.sort_directions = def_order.order_by_fields_directions;

        this.current_sort_field_ids = [...this.options.sort_by_field_ids];
        this.current_sort_directions = [...this.options.sort_directions];

        this.data = this.#getData();
        this.filtered_data = [...this.data];

        this.sorted_data = this.#sortData(this.options.sort_by_field_ids, this.options.sort_directions);

        this.#render(0, this.options.show_rows_num);

        if (this.options.show_pagination) {
            this.#renderPagination();
        }

        if (this.options.show_filter) {
            this.#renderFilter();
        }
    };

    #getDefaultOrderByFields = () => {
        if (!this.element.dataset.defaultOrderBy)
            return {
                order_by_fields_id: [DEF_SORT_BY_FIELD_ID],
                order_by_fields_directions: [DEF_SORT_DIRECTION]
            };

        const order_by_fields = this.element.dataset.defaultOrderBy.split(",");
        let order_by_fields_id = [];
        let order_by_fields_directions = [];

        order_by_fields.forEach(f => {
            let f_name = f.split(":")[0];
            let f_direction = f.split(":")[1];

            let field_id = this.fields.findIndex(field => field.name === f_name);
            if (field_id !== -1 && !order_by_fields_id.includes(field_id)) {
                order_by_fields_id.push(field_id);
                order_by_fields_directions.push(f_direction);
            }
        })

        if (order_by_fields_id.length === 0) {
            order_by_fields_id.push(DEF_SORT_BY_FIELD_ID);
            order_by_fields_directions.push(DEF_SORT_DIRECTION);
        }

        return { order_by_fields_id, order_by_fields_directions };
    }

    #getFields = () => {
        let fields = [];

        this.element.querySelectorAll("thead > tr > th").forEach((e, i) => {
            let text = e.innerText;
            e.innerText = "";

            let text_btn = document.createElement("button");
            text_btn.classList.add("vdt-name");
            text_btn.innerText = text;
            e.appendChild(text_btn);

            text_btn.addEventListener("click", this.#onFieldNameClick(i));

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

            const first_data = this.element.querySelector(`tbody > tr:first-of-type > td:nth-of-type(${i + 1})`).innerText;

            let field_type = "text";
            if (TIME_FIELD_PATTERN.test(first_data)) field_type = "time";
            else if (/^\d+$/.test(first_data)) field_type = "number";

            fields.push({
                name: e.dataset.fieldName,
                text: text,
                type: field_type,
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

    #sortData = (field_ids, directions) => {
        const by = directions.map(dir => dir === "asc" ? 1 : -1);
        const test = (a, b, i) => {
            let sign = a > b ? 1 : -1;
            return sign ? (by[i]) * sign : 0;
        }

        const sorted_data = this.filtered_data.sort((a, b) => {
            return field_ids.map((id, i) => {
                const field = this.fields[id];
                let a_value = a[field.name].data;
                let b_value = b[field.name].data;

                switch (field.type) {
                    case "time":
                        const time_field_matches_a = TIME_FIELD_PATTERN.exec(a_value);
                        const time_field_matches_b = TIME_FIELD_PATTERN.exec(b_value);

                        a_value = parseInt(time_field_matches_a[1]) * 60 + parseInt(time_field_matches_a[2], 10);
                        b_value = parseInt(time_field_matches_b[1]) * 60 + parseInt(time_field_matches_b[2], 10);
                        break;
                    case "number":
                        a_value = parseInt(a_value, 10);
                        b_value = parseInt(b_value, 10);
                        break;
                }

                return test(a_value, b_value, i)
            }).filter(item => item !== 0)[0];
        });

        return [...sorted_data];
    };

    #renderPagination = () => {
        let num_results = this.filtered_data.length;
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

    #renderFilter = () => {
        let old_filter = this.element.previousElementSibling;
        if (old_filter && old_filter.classList.contains("vdt-filter")) {
            old_filter.remove();
        }

        let filter_div = document.createElement("div");
        filter_div.classList.add("vdt-filter");
        filter_div.innerHTML = "";

        this.element.insertAdjacentElement("beforebegin", filter_div);

        let filter_input = document.createElement("input");
        filter_input.type = "text";
        filter_input.placeholder = "Filtra classifica per...";
        filter_input.addEventListener("keyup", this.#onFilterInput);
        filter_div.appendChild(filter_input);
    }

    #render = (start, qty) => {
        this.current_start = start;

        this.fields.forEach((e, i) => {
            e.element.classList.remove("vdt-asc", "vdt-desc");
            let sort_field_id = this.current_sort_field_ids.indexOf(i);

            if (sort_field_id !== -1) {
                e.element.classList.add("vdt-" + this.current_sort_directions[sort_field_id]);
            }
        });

        this.data.forEach(e => {
            e.row_element.remove();
        });

        this.sorted_data.forEach((e, i) => {
            if (i >= start && i < start + qty) {
                this.element.appendChild(e.row_element);
            }
        });
    };

    #onFieldNameClick = (field_id) => {
        return (e) => {
            let field_id_pos = this.current_sort_field_ids.indexOf(field_id);
            let direction = field_id_pos !== -1 ? this.current_sort_directions[field_id_pos] : "asc";

            if (field_id_pos === -1) {
                this.current_sort_field_ids.push(field_id);
                this.current_sort_directions.push("asc");
            } else if (field_id_pos !== -1 && direction === "asc") {
                this.current_sort_directions[field_id_pos] = "desc";
            } else if (field_id_pos !== -1 && direction === "desc") {
                this.current_sort_field_ids.splice(field_id_pos, 1);
                this.current_sort_directions.splice(field_id_pos, 1);
            }

            // if (this.current_sort_field_ids.length === 0) {
            //     this.current_sort_field_ids = [...this.options.sort_by_field_ids];
            //     this.current_sort_directions = [...this.options.sort_directions];
            // }

            this.sorted_data = this.#sortData(this.current_sort_field_ids, this.current_sort_directions);
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

    #onFilterInput = (e) => {
        let filter_value = e.target.value.toLowerCase();

        if (filter_value === "") {
            this.filtered_data = [...this.data];
        } else {
            let filtered_data = this.data.filter(row => {
                let row_data = this.fields.map(field => row[field.name].data.toLowerCase());
                return row_data.some(rd => rd.includes(filter_value));
            });

            this.filtered_data = [...filtered_data];
        }

        this.sorted_data = this.#sortData(this.current_sort_field_ids, this.current_sort_directions);
        this.#render(this.current_start, this.current_show_rows_num);
        this.#renderPagination();
    };
}
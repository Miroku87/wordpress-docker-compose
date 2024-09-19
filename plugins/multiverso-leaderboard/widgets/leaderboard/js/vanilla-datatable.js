export default class VanillaDataTable {
    constructor(element, options = {}) {
        this.element = element;
        this.fields = [];
        this.data = [];
        this.sorted_data = [];
        this.current_start = 0;

        this.options = {
            show_rows_num: options.show_rows_num || 5,
            sort_by_field_id: options.sort_by_field_id || 0,
            sort_direction: options.sort_direction || "asc",
            show_pagination: typeof options.show_pagination === "boolean" ? options.show_pagination : true,
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
    };

    #getFields = () => {
        let fields = [];

        this.element.querySelectorAll("thead > tr > th").forEach((e, i) => {
            let name = e.innerText;
            e.innerText = "";

            let name_btn = document.createElement("button");
            name_btn.classList.add("name");
            name_btn.innerText = name;
            e.appendChild(name_btn);

            name_btn.addEventListener("click", this.#onFieldNameClick(i));

            let arrows_div = document.createElement("div");
            arrows_div.classList.add("arrows");
            arrows_div.innerText = ""; //"▲▼";
            e.appendChild(arrows_div);

            let arrow_up = document.createElement("span");
            arrow_up.classList.add("up");
            arrow_up.innerText = "▲";
            arrows_div.appendChild(arrow_up);

            let br = document.createElement("br");
            arrows_div.appendChild(br);

            let arrow_down = document.createElement("span");
            arrow_down.classList.add("down");
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

    #renderPagination = (current_page) => {
        let num_results = this.data.length;
    };

    #render = (start, qty) => {
        this.current_start = start;

        this.fields.forEach((e, i) => {
            e.element.classList.remove("asc", "desc");

            if (i === this.current_sort_field_id) {
                e.element.classList.add(this.current_sort_direction);
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
}
import { find } from "lodash";

export default class Validation {
    constructor() {
        this.messages = {};
    }

    setMessages(messages) {
        this.messages = messages;
    }

    getMessage(field, multiple = false, file = false) {
        if (multiple) {
            if (
                find(this.messages, (message, fieldName) =>
                    fieldName.startsWith(`${field}.`)
                )
            ) {
                if (file) {
                    return `Invalid file selected or the size exceeds the limit`;
                } else {
                    return `A field is left empty or invalid date entered`;
                }
            }
        } else {
            if (this.messages[field]) {
                if (this.messages[field][0].includes("id")) {
                    return this.messages[field][0].replace(/id/gi, "");
                }

                return this.messages[field][0];
            }
        }
    }

    hasErrors() {
        return Object.keys(this.messages).length > 0;
    }
}

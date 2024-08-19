<template>
    <v-row v-if="unclearedCheques.length">
        <v-col cols="12">
            <v-card style="border: 2px solid red">
                <v-card-subtitle>
                    <span class="font-weight-bold red--text"
                        >{{ unclearedCheques.length }} Uncleared Cheque(s)</span
                    >

                    <span class="float-right">
                        <a
                            href="#"
                            @click.prevent="bulkMarkAsCleared"
                            class="accent-3 text-decoration-none float-right font-weight-bold"
                        >
                            Mark all as cleared
                        </a>
                    </span>
                </v-card-subtitle>
                <v-card-text>
                    <v-list three-line>
                        <template v-for="(item, index) in unclearedCheques">
                            <v-list-item :key="item.id">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <v-icon class="red--text accent-3"
                                            >mdi-shield-alert</v-icon
                                        >
                                        Cheque#
                                        <strong>{{ item.cheque_no }}</strong> of
                                        {{ money(item.amount) }} is uncleared
                                        and the deadline is reached
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        <span
                                            class="d-block"
                                            style="margin-left: 27px"
                                            >{{ item.description }}</span
                                        >

                                        <a
                                            href="#"
                                            @click.prevent="
                                                markChequesAsCleared(item.id)
                                            "
                                            class="success--text accent-3 text-decoration-none float-right"
                                        >
                                            Mark as cleared
                                        </a>
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>

                            <v-divider></v-divider>
                        </template>
                    </v-list>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>
<script>
import { mapActions } from "vuex/dist/vuex.common.js";
import CurrencyMixin from "../../../mixins/CurrencyMixin";
export default {
    props: ["unclearedCheques"],

    mixins: [CurrencyMixin],

    methods: {
        ...mapActions({
            markChequesAsCleared: "dashboard/markChequesAsCleared",
            markAllChequesAsCleared: "dashboard/markAllChequesAsCleared",
        }),

        bulkMarkAsCleared() {
            if (confirm("Are you sure")) {
                this.markAllChequesAsCleared();
            }
        },
    },
};
</script>

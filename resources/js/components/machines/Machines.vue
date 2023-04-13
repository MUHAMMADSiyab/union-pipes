<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Machines</h5>

            <v-row>
                <v-col cols="12">
                    <v-data-table
                        :headers="headers"
                        :items="machines"
                        class="elevation-1"
                        item-key="id"
                        :search="search"
                        :items-per-page="perPage"
                        :loading="loading"
                        :show-select="can('machine_delete') && !printMode"
                        loading-text="Loading machines..."
                        :footer-props="footerProps"
                        v-model="selectedItems"
                    >
                        <!-- S# -->
                        <template slot="item.sno" slot-scope="props">{{
                            props.index + 1
                        }}</template>

                        <!-- Logo -->
                        <template
                            slot="item.logo"
                            slot-scope="props"
                            v-if="props.item.logo"
                        >
                            <v-img
                                :aspect-ratio="16 / 9"
                                width="50"
                                :src="props.item.logo"
                                class="rounded"
                                @click="showLogoDialog(props.item.logo)"
                            ></v-img>
                        </template>

                        <!-- Top -->
                        <template v-slot:top v-if="!printMode">
                            <v-btn
                                color="error"
                                small
                                :disabled="!selectedItems.length"
                                class="ma-2 text-right"
                                @click="deleteMultiple"
                                v-if="can('machine_delete')"
                                ><v-icon left>mdi-trash-can-outline</v-icon>
                                Delete Selected</v-btn
                            >

                            <v-btn
                                color="success"
                                small
                                link
                                to="/machines/add"
                                class="ma-2 text-right"
                                v-if="can('machine_create')"
                            >
                                <v-icon left>mdi-account-plus-outline</v-icon>
                                New Machine</v-btn
                            >

                            <Excel
                                module="machines"
                                :ids="selectedItems.map((item) => item.id)"
                            />
                            <CSV
                                module="machines"
                                :ids="selectedItems.map((item) => item.id)"
                            />
                            <PDF
                                module="machines"
                                :ids="selectedItems.map((item) => item.id)"
                            />

                            <v-text-field
                                v-model="search"
                                placeholder="Search"
                                class="mx-4"
                                append-icon="mdi-magnify"
                            ></v-text-field>
                        </template>

                        <!-- Per Kg Price -->
                        <template slot="item.per_kg_price" slot-scope="props">
                            {{ money(props.item.per_kg_price) }}
                        </template>

                        <!-- Actions -->
                        <template slot="item.actions" slot-scope="props">
                            <v-btn
                                x-small
                                text
                                color="primary"
                                @click="showEditDialog(props.item.id)"
                                title="Edit"
                                v-if="can('machine_edit')"
                            >
                                <v-icon small>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn
                                x-small
                                text
                                color="red darken-2"
                                @click="setMachineId(props.item.id)"
                                title="Delete"
                                v-if="can('machine_delete')"
                            >
                                <v-icon small>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                    </v-data-table>

                    <!-- Edit dialog -->
                    <v-dialog v-model="editDialog" max-width="600" persistent>
                        <EditMachine
                            :single-machine="machine"
                            v-if="machine"
                            @closeDialog="closeEditDialog"
                        />
                    </v-dialog>

                    <!-- Logo dialog -->
                    <v-dialog v-model="logoDialog" width="400">
                        <v-card>
                            <v-img
                                width="400"
                                :src="currentLogo"
                                class="rounded"
                            ></v-img>
                        </v-card>
                    </v-dialog>

                    <!-- Confirmation -->
                    <Confirmation
                        ref="confirmationComponent"
                        :id="machineId"
                        @confirmDeletion="
                            machineId
                                ? handleMachineDelete()
                                : handleMultipleMachinesDelete()
                        "
                    />
                </v-col>
            </v-row>

            <alert />
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../mixins/DatatableMixin";
import EditMachine from "./EditMachine.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";
import CurrencyMixin from "../../mixins/CurrencyMixin";

export default {
    mixins: [DatatableMixin, CurrencyMixin],

    components: {
        EditMachine,
        Navbar,
        Confirmation,
        Excel,
        CSV,
        PDF,
    },

    data() {
        return {
            editDialog: false,
            logoDialog: false,
            headers: [
                { text: "S#", value: "sno" },
                { text: "Machine Name", value: "name" },
                { text: "Actions", value: "actions", align: " d-print-none" },
            ],
            selectedItems: [],
            machineId: null,
            currentLogo: null,
        };
    },

    methods: {
        ...mapActions({
            getMachines: "machine/getMachines",
            getMachine: "machine/getMachine",
            deleteMachine: "machine/deleteMachine",
            deleteMultipleMachines: "machine/deleteMultipleMachines",
        }),

        showEditDialog(id) {
            this.editDialog = true;

            this.getMachine(id);
        },

        closeEditDialog() {
            this.editDialog = false;
        },

        setMachineId(id) {
            this.machineId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMachineDelete() {
            await this.deleteMachine(this.machineId);
            this.machineId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMultipleMachinesDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultipleMachines(ids);

            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },
    },

    computed: {
        ...mapGetters({
            machines: "machine/machines",
            machine: "machine/machine",
            loading: "loading",
        }),
    },

    mounted() {
        // remove actions if no access is given
        if (!this.can("machine_edit") && !this.can("machine_delete")) {
            this.headers = this.headers.filter(
                (header) => header.value !== "actions"
            );
        }
        this.getMachines();
    },
};
</script>

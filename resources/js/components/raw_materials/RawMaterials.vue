<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Raw Materials</h5>
            <v-data-table
                :headers="headers"
                :items="raw_materials"
                class="elevation-1"
                item-key="id"
                :search="search"
                :items-per-page="perPage"
                :loading="loading"
                :show-select="can('raw_material_delete') && !printMode"
                loading-text="Loading raw_materials..."
                :footer-props="footerProps"
                v-model="selectedItems"
            >
                <!-- S# -->
                <template slot="item.sno" slot-scope="props">{{
                    props.index + 1
                }}</template>

                <!-- Month -->
                <template slot="item.month" slot-scope="props">
                    <span>
                        {{
                            new Date(props.item.month).toLocaleDateString(
                                "en-US",
                                { month: "long", year: "numeric" }
                            )
                        }}
                    </span>
                </template>
                <template slot="item.entries_sum_amount" slot-scope="props">
                    <span>{{ money(props.item.entries_sum_amount) }}</span>
                </template>

                <!-- Top -->
                <template v-slot:top v-if="!printMode">
                    <v-btn
                        color="error"
                        small
                        :disabled="!selectedItems.length"
                        class="ma-2 text-right"
                        @click="deleteMultiple"
                        v-if="can('raw_material_delete')"
                        ><v-icon left>mdi-trash-can-outline</v-icon> Delete
                        Selected</v-btn
                    >

                    <v-btn
                        color="success"
                        small
                        link
                        to="/raw_materials/add"
                        class="ma-2 text-right"
                        v-if="can('raw_material_create')"
                    >
                        <v-icon left>mdi-raw_material-plus</v-icon>
                        New Raw Material Entry</v-btn
                    >

                    <Excel
                        module="raw_materials"
                        :ids="selectedItems.map((item) => item.id)"
                    />
                    <CSV
                        module="raw_materials"
                        :ids="selectedItems.map((item) => item.id)"
                    />
                    <PDF
                        module="raw_materials"
                        :ids="selectedItems.map((item) => item.id)"
                    />

                    <v-text-field
                        v-model="search"
                        placeholder="Search"
                        class="mx-4"
                        append-icon="mdi-magnify"
                    ></v-text-field>
                </template>

                <!-- Actions -->
                <template slot="item.actions" slot-scope="props">
                    <v-btn
                        x-small
                        text
                        color="indigo"
                        :to="`/raw_materials/${props.item.id}`"
                        title="Raw Material Entries"
                        v-if="can('raw_material_show')"
                    >
                        <v-icon small>mdi-format-list-checkbox</v-icon>
                    </v-btn>
                    <v-btn
                        x-small
                        text
                        color="primary"
                        :to="`/raw_materials/edit/${props.item.id}`"
                        title="Edit"
                        v-if="can('raw_material_edit')"
                    >
                        <v-icon small>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn
                        x-small
                        text
                        color="red darken-2"
                        @click="setRawMaterialId(props.item.id)"
                        title="Delete"
                        v-if="can('raw_material_delete')"
                    >
                        <v-icon small>mdi-delete</v-icon>
                    </v-btn>
                </template>
            </v-data-table>

            <!-- Confirmation -->
            <Confirmation
                ref="confirmationComponent"
                :id="rawMaterialId"
                @confirmDeletion="
                    rawMaterialId
                        ? handleRawMaterialDelete()
                        : handleMultipleRawMaterialsDelete()
                "
            />
        </v-container>
        <alert />
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../mixins/DatatableMixin";
import Navbar from "../navs/Navbar";
import Confirmation from "../globals/Confirmation";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";
import CurrencyMixin from "../../mixins/CurrencyMixin";

export default {
    mixins: [DatatableMixin, CurrencyMixin],

    components: {
        Navbar,
        Confirmation,
        Excel,
        CSV,
        PDF,
    },

    data() {
        return {
            headers: [
                { text: "S#", value: "sno" },
                { text: "Month", value: "month" },
                { text: "Total Amount", value: "entries_sum_amount" },
                { text: "Actions", value: "actions", align: " d-print-none" },
            ],
            selectedItems: [],

            rawMaterialId: null,
        };
    },

    methods: {
        ...mapActions({
            getRawMaterials: "raw_material/getRawMaterials",
            getRawMaterial: "raw_material/getRawMaterial",
            deleteRawMaterial: "raw_material/deleteRawMaterial",
            deleteMultipleRawMaterials:
                "raw_material/deleteMultipleRawMaterials",
        }),

        setRawMaterialId(id) {
            this.rawMaterialId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleRawMaterialDelete() {
            await this.deleteRawMaterial(this.rawMaterialId);
            this.rawMaterialId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMultipleRawMaterialsDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultipleRawMaterials(ids);

            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },
    },

    computed: {
        ...mapGetters({
            raw_materials: "raw_material/raw_materials",
            raw_material: "raw_material/raw_material",
            loading: "loading",
        }),
    },

    mounted() {
        // remove actions if no access is given
        if (
            !this.can("raw_material_edit") &&
            !this.can("raw_material_delete")
        ) {
            this.headers = this.headers.filter(
                (header) => header.value !== "actions"
            );
        }

        this.getRawMaterials();
    },
};
</script>

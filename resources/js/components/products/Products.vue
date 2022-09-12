<template>
  <div>
    <Navbar v-if="!printMode" />

    <print-button />

    <v-container class="mt-4">
      <h5 class="text-subtitle-1 mb-2">Products</h5>

      <v-row>
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="products"
            class="elevation-1"
            item-key="id"
            :search="search"
            :items-per-page="perPage"
            :loading="loading"
            :show-select="can('product_delete') && !printMode"
            loading-text="Loading products..."
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
                v-if="can('product_delete')"
                ><v-icon left>mdi-trash-can-outline</v-icon> Delete
                Selected</v-btn
              >

              <v-btn
                color="success"
                small
                link
                to="/products/add"
                class="ma-2 text-right"
                v-if="can('product_create')"
              >
                <v-icon left>mdi-account-plus-outline</v-icon>
                New Product</v-btn
              >

              <Excel
                module="products"
                :ids="selectedItems.map((item) => item.id)"
              />
              <CSV
                module="products"
                :ids="selectedItems.map((item) => item.id)"
              />
              <PDF
                module="products"
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
                color="primary"
                @click="showEditDialog(props.item.id)"
                title="Edit"
                v-if="can('product_edit')"
              >
                <v-icon small>mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                x-small
                text
                color="red darken-2"
                @click="setProductId(props.item.id)"
                title="Delete"
                v-if="can('product_delete')"
              >
                <v-icon small>mdi-delete</v-icon>
              </v-btn>
            </template>
          </v-data-table>

          <!-- Edit dialog -->
          <v-dialog v-model="editDialog" max-width="600" persistent>
            <EditProduct
              :single-product="product"
              v-if="product"
              @closeDialog="closeEditDialog"
            />
          </v-dialog>

          <!-- Logo dialog -->
          <v-dialog v-model="logoDialog" width="400">
            <v-card>
              <v-img width="400" :src="currentLogo" class="rounded"></v-img>
            </v-card>
          </v-dialog>

          <!-- Confirmation -->
          <Confirmation
            ref="confirmationComponent"
            :id="productId"
            @confirmDeletion="
              productId ? handleProductDelete() : handleMultipleProductsDelete()
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
import EditProduct from "./EditProduct.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";

export default {
  mixins: [DatatableMixin],

  components: {
    EditProduct,
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
        { text: "Product Name", value: "name" },
        { text: "Actions", value: "actions", align: " d-print-none" },
      ],
      selectedItems: [],
      productId: null,
      currentLogo: null,
    };
  },

  methods: {
    ...mapActions({
      getProducts: "product/getProducts",
      getProduct: "product/getProduct",
      deleteProduct: "product/deleteProduct",
      deleteMultipleProducts: "product/deleteMultipleProducts",
    }),

    showEditDialog(id) {
      this.editDialog = true;

      this.getProduct(id);
    },

    closeEditDialog() {
      this.editDialog = false;
    },

    setProductId(id) {
      this.productId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleProductDelete() {
      await this.deleteProduct(this.productId);
      this.productId = null;
      this.$refs.confirmationComponent.setDialog(false);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultipleProductsDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultipleProducts(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];
    },
  },

  computed: {
    ...mapGetters({
      products: "product/products",
      product: "product/product",
      loading: "loading",
    }),
  },

  mounted() {
    // remove actions if no access is given
    if (!this.can("product_edit") && !this.can("product_delete")) {
      this.headers = this.headers.filter(
        (header) => header.value !== "actions"
      );
    }
    this.getProducts();
  },
};
</script>
<template>
  <Head title="SultraSpot | Checkout" />

  <Layout>
    <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Transaksi /</span>
        Checkout
      </h4>

      <div class="row px-md-4 px-1">
        <div class="col-md-12">
          <div class="card">
            <h6 class="card-header text-light">
              Harga dapat berubah berdasarkan promosi / nilai tukar uang.
              Beberapa item mungkin tidak ada stok.
            </h6>
            <div class="table-responsive text-nowrap pb-3">
              <table class="table table-borderless">
                <thead>
                  <tr class="fw-bold">
                    <th style="width: 10%">Item</th>
                    <th style="max-width: 100px"></th>
                    <th style="width: 10%" class="text-end">Harga</th>
                    <th style="width: 10%" class="text-end">Jumlah</th>
                    <th style="width: 10%" class="text-end">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <img
                        :src="item_thumbnail"
                        alt="thumbnail.jpg"
                        class="img-thumbnail"
                        width="70"
                      />
                    </td>
                    <td>
                      <span class="fw-bold">
                        <Link
                          :href="route('wisata.detail', place.id)"
                          class="text-dark"
                        >
                          {{ place.name }}
                        </Link>
                      </span>
                      <br />
                      <span>{{ place.city }}</span> <br />
                      <span>{{ place.address }}</span>
                    </td>
                    <td class="text-end">Rp {{ place.price }}</td>
                    <td class="text-end">{{ data.qty }}</td>
                    <td class="text-end">Rp {{ place.price * data.qty }}</td>
                  </tr>
                  <tr>
                    <td colspan="5"><hr /></td>
                  </tr>
                  <tr>
                    <td colspan="3">
                      <Link
                        :href="route('list-wisata')"
                        class="btn btn-sm btn-success"
                      >
                        Lihat Semua Wisata
                      </Link>
                    </td>
                    <td class="fw-bold">Diskon</td>
                    <td class="text-end fw-bold">-</td>
                  </tr>
                  <tr v-if="place.rental">
                    <td colspan="3"></td>
                    <td>Sewa Kamera</td>
                    <td class="text-end">Rp {{ data.price_kamera }}</td>
                  </tr>
                  <tr>
                    <td colspan="3"></td>
                    <td class="fw-bold">Total (IDR)</td>
                    <td class="text-end fw-bold">
                      Rp {{ data.total_payment }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="row px-md-4 px-1 mt-4 justify-content-end">
        <div class="col-md-4">
          <div class="card">
            <div class="card-body text-center">
              <div class="fs-5 fw-bold text-primary">
                Total : Rp. {{ data.total_payment }}
              </div>
              <br />
              <div>
                <button
                  @click="orderNowStore()"
                  class="btn btn-lg btn-success fw-bold fs-5"
                >
                  CHECKOUT
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <hr class="my-5" />
    </div>
  </Layout>
</template>

<script>
import Layout from "../Shared/Template.vue";
import { Link, Head, useForm } from "@inertiajs/inertia-vue3";
import ItemThumbnail from "../../../../img/elements/2.jpg";
import Swal from "sweetalert2";
import { Inertia } from "@inertiajs/inertia";

export default {
  components: {
    Layout,
    Link,
    Head,
  },

  data() {
    return {
      item_thumbnail: ItemThumbnail,
      base: window.location.origin,
      form: useForm({
        tour_place_id: this.place.id,
        quantity: parseInt(this.data.qty),
        total_payment: this.place.price * this.data.qty,
        rental: this.data.sewa_kamera,
        price_kamera: this.data.price_kamera,
      }),
    };
  },

  methods: {
    orderNowStore() {
      Swal.fire({
        title: "Konfirmasi?",
        text: "Cek kembali item Anda sebelum checkout.",
        icon: "info",
        showCancelButton: true,
        cancelButtonText: "Kembali",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Lanjut",
      }).then((result) => {
        if (result.isConfirmed) {
          Inertia.post(`${this.base}/order`, this.form, {
            onSuccess: () =>
              Swal.fire("Sukses!", `Checkout berhasil.`, "success"),
          });
        }
      });
    },
  },

  props: {
    place: Object,
    data: Object,
  },
};
</script>

<style>
</style>
<template>
	<div class="form-bible-equivalents">
		<div class="columns is-multiline" v-for="(link, index) in equivalents">
			<div class="column is-10-desktop is-6-tablet is-offset-1-desktop">
			<div class="card mb20">
				<header class="card-header">
					<p class="card-header-title" v-if="link.title"> {{ link.title }} <span v-if="link.organization">&nbsp;| {{ link.organization }}</span></p>
				</header>
				<div class="card-content">
					<div class="content columns is-multiline">
						<label class="label is-4-desktop column">Title <input v-model="link.title" type="text" name="equivalents[][title]" class="input" placeholder="Title"></label>
						<label class="label is-2-desktop column">Type <input v-model="link.type" type="text" name="equivalents[][type]" class="input" placeholder="Type"></label>
						<label class="label is-3-desktop column">Url <input v-model="link.url" type="text" name="equivalents[][url]" class="input" placeholder="url"></label>
						<label class="label is-3-desktop column">Organization <input v-model="link.organization" type="text" name="equivalents[][type]" class="input" placeholder="Organization"></label>
					</div>
				</div>
				<footer class="card-footer">
					<a type="button" tabindex="0" v-on:keyup.13="addLink" v-on:click="addLink" class="card-footer-item">Add Link</a>
					<a type="button" tabindex="0" v-on:keyup.13="removeLink(index)" v-on:click="removeLink(index)" class="card-footer-item">Remove -</a>
				</footer>
			</div>
			</div>
		</div>
	</div>
</template>

<script>
    export default {
	    data() {
			return {
				link: {
					url: '',
					type: ''
				},
		    	equivalents: []
			}
	    },
	    mounted() {
		    this.equivalents = JSON.parse(this.$el.dataset.equivalents)
	    },
		methods: {
			addLink: function () {
				this.equivalents.push(Vue.util.extend({}, this.link))
			},
			removeLink: function (index) {
				Vue.delete(this.equivalents, index);
			}
		}
    }
</script>
<div class="stm-lms-payments">

	<div class="stm-lms-payment_method" v-for="(payment_info, payment) in payments">

		<div class="stm-lms-payment_header" @click="togglePayment(payment, event)" :class="{active: payment_info.displayShow}">
			<div class="stm-lms-payment_header_info">
				<div class="stm-lms-payment_header_img">
					<img :src="'<?php echo esc_url( STM_LMS_URL ); ?>/assets/img/payments/' + payment_info.img"
						:alt="payment_info.name"
						v-if="payment_info.img"
						width="40" height="40">
				</div>
				<div class="stm-lms-payment_header_block">
					<div class="stm-lms-payment_header_title">
						{{ payment_info.name }}
					</div>
					<div class="stm-lms-payment_header_block_description" v-if="payment_info.payment_description" >
						<div class="stm-lms-payment_info_block_hint">
							<i class="stmlms-info-circle"></i>
						</div>
						<span v-if="payment_info.payment_description" v-html="payment_info.payment_description"></span>
					</div>
				</div>
			</div>

			<div class="stm-lms-payment_header-toggle">
				<div class="wpcfto-admin-checkbox" @click.stop>
					<label>
						<div class="wpcfto-admin-checkbox-wrapper is_toggle" :class="{active: payment_info.enabled}">
							<div class="wpcfto-checkbox-switcher"></div>
							<input type="checkbox" v-model="payment_info.enabled">
						</div>
					</label>
				</div>

				<div class="stm-lms-payment_header-toggle-arrow" :class="{rotate: payment_info.displayShow}">
					<i class="stmlms-chevron_down"></i>
				</div>
			</div>
		</div>

		<transition name="slide-fade">
			<div class="stm-lms-payment_info" v-if="payment_info.displayShow">
				<div class="stm-lms-payment_info_field" v-for="(field_info, field_name) in payment_info.fields">

					<div class="stm-lms-payment_info_block" v-if="field_info.info_title">
						<div class="stm-lms-payment_info_block_title">
							{{ field_info.info_title }}
						</div>
						<div class="stm-lms-payment_info_block_description" v-if="field_info.info_description" >
							<span v-html="field_info.info_description"></span>
						</div>
					</div>

					<textarea v-if="field_info['type'] == 'textarea'"
							v-bind:placeholder="field_info['placeholder']"
							v-model="payments[payment].fields[field_name].value">
					</textarea>

					<div class="stm-lms-payment_content" v-if="field_info['type'] == 'text'">
						<input type="text"
								v-bind:placeholder="field_info['placeholder']"
								v-model="payment_info.fields[field_name].value"
								v-bind:readonly="field_info['readonly']"
								v-bind:id="payment + _ +field_name"
								@click="handleInputClick(field_info, payment + _ + field_name)">

						<div v-if="activeTooltip === payment + _ + field_name" class="readonly-tooltip">copied_text</div>
					</div>

					<select v-if="field_info['type'] == 'select'"
							v-model="payment_info.fields[field_name].value">
						<option v-for="(option_value, option_name) in sources[field_info['source']]" v-bind:value="option_value">
							{{ option_name }}
						</option>
					</select>

				</div>
			</div>
		</transition>

	</div>
</div>

<div hidden aria-hidden="true"><label>{{ __('website.submissions__consent_website') }}<input name="website" tabindex="-1" autocomplete="off"></label></div>
<label class="template-consent"><input name="privacy" type="checkbox" value="1" @checked($privacyChecked ?? old('privacy')) required><span>{{ __('cms.privacy_consent') }}</span></label>

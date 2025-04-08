<form method='post'>
    <fieldset>
        <legend>{__('customerFilters')}</legend>

        <div class='fieldset-body'>
            <div class='form-group form-row align-items-center'>
                <label class='col col-sm-4 col-form-label text-sm-right' for='filter[active]'>
                    {__('activeCustomers')}
                </label>
                <div class='col-sm pl-sm-3 pr-sm-5 order-last order-sm-2'>
                    <div class='custom-control custom-checkbox'>
                        <input class='custom-control-input' name='filter[active]'
                               id='filter[active]' type='checkbox' checked
                        />

                        <label class='custom-control-label' for='filter[active]'></label>
                    </div>
                </div>
            </div>

            <div class='form-group form-row align-items-center'>
                <label class='col col-sm-4 col-form-label text-sm-right'
                       for='filter[group]'>{__('customerGroup')}</label>
                <span class='col-sm pl-sm-3 pr-sm-5 order-last order-sm-2'>
                    <select class='custom-select' id='filter[group]' name='filter[group]'>
                        <option value=''>{__('all')}</option>
                        {foreach from=$customerGroups item=group}
                            <option value='{$group->getID()}'>
                                {__($group->getName())}
                            </option>
                        {/foreach}
                    </select>
                </span>
            </div>

            <div class='form-group form-row align-items-center'>
                <label class='col col-sm-4 col-form-label text-sm-right'
                       for='filter[language]'>{__('language')}</label>
                <span class='col-sm pl-sm-3 pr-sm-5 order-last order-sm-2'>
                    <select class='custom-select' id='filter[language]' name='filter[language]'>
                        <option value=''>{__('all')}</option>
                        {foreach from=$filterLanguages item=language}
                            <option value='{$language->getId()}'>{__($language->getLocalizedName())}</option>
                        {/foreach}
                    </select>
                </span>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>{__('messageSettings')}</legend>

        <div class='form-group form-row align-items-center'>
            <label class='col col-sm-4 col-form-label text-sm-right' for='msgType'>
                {__('msgType')}
            </label>

            <div class='col-sm pl-sm-3 pr-sm-5 order-last order-sm-2'>
                <div class='custom-control custom-radio'>
                    <input type='radio' class='custom-control-input' id='msgTypeSms'
                           name='msgType' value='sms' checked>
                    <label class='custom-control-label' for='msgTypeSms'>
                        {__('SMS')}
                    </label>
                </div>

                <div class='custom-control custom-radio'>
                    <input type='radio' class='custom-control-input' id='msgTypeVoice'
                           name='msgType' value='voice'>
                    <label class='custom-control-label' for='msgTypeVoice'>
                        {__('Voice')}
                    </label>
                </div>
            </div>
        </div>

        <div class='form-group form-row align-items-center' id='wrapFlash'>
            <label class='col col-sm-4 col-form-label text-sm-right' for='flash'
                   data-toggle='tooltip' data-placement='left'
                   title='{__("flashDescription")}'>
                {__('flash')}
            </label>
            <div class='col-sm pl-sm-3 pr-sm-5 order-last order-sm-2'>
                <div class='custom-control custom-checkbox'>
                    <input class='custom-control-input' name='flash'
                           id='flash' type='checkbox'
                    />

                    <label class='custom-control-label' for='flash'></label>
                </div>
            </div>
        </div>

        <div class='form-group form-row align-items-center' id='wrapPerformanceTracking'>
            <label class='col col-sm-4 col-form-label text-sm-right'
                   for='performanceTracking' data-toggle='tooltip' data-placement='left'
                   title='{__("performanceTrackingDescription")}'>
                {__('performanceTracking')}
            </label>
            <div class='col-sm pl-sm-3 pr-sm-5 order-last order-sm-2'>
                <div class='custom-control custom-checkbox'>
                    <input class='custom-control-input' name='performanceTracking'
                           id='performanceTracking' type='checkbox'
                    />

                    <label class='custom-control-label' for='performanceTracking'></label>
                </div>
            </div>
        </div>

        <div class='form-group form-row align-items-center' id='wrapDelay'>
            <label class='col col-sm-4 col-form-label text-sm-right' for='delay'
                   data-toggle='tooltip' data-placement='left'
                   title='{__("delayDescription")}'>
                {__('delay')}
            </label>
            <div class='col-sm pl-sm-3 pr-sm-5 order-last order-sm-2'>
                <input class='form-control' id='delay' name='delay'/>
            </div>
        </div>

        <div class='form-group form-row align-items-center' id='wrapForeignId'>
            <label class='col col-sm-4 col-form-label text-sm-right' for='foreignId'
                   data-toggle='tooltip' data-placement='left'
                   title='{__("foreignIdDescription")}'>
                {__('foreignId')}
            </label>
            <div class='col-sm pl-sm-3 pr-sm-5 order-last order-sm-2'>
                <input class='form-control' maxlength='64' id='foreignId'
                       name='foreignId'/>
            </div>
        </div>

        <div class='form-group form-row align-items-center' id='wrapLabel'>
            <label class='col col-sm-4 col-form-label text-sm-right' for='label'
                   data-toggle='tooltip' data-placement='left'
                   title='{__("labelDescription")}'>
                {__('label')}
            </label>
            <div class='col-sm pl-sm-3 pr-sm-5 order-last order-sm-2'>
                <input class='form-control' maxlength='100' id='label' name='label'/>
            </div>
        </div>

        <div class='fieldset-body'>
            <div class='form-group form-row align-items-center'>
                <label class='col col-sm-4 col-form-label text-sm-right' for='to'>
                    {__('recipients')}
                </label>
                <div class='col-sm pl-sm-3 pr-sm-5 order-last order-sm-2'>
                    <input class='form-control' id='to' name='to'/>
                </div>
            </div>

            <div class='form-group form-row align-items-center'>
                <label class='col col-sm-4 col-form-label text-sm-right' for='from'>
                    {ucfirst(__('from'))}
                </label>
                <div class='col-sm pl-sm-3 pr-sm-5 order-last order-sm-2'>
                    <input class='form-control' maxlength='16' id='from' name='from'/>
                </div>
            </div>

            <div class='form-group form-row align-items-center'>
                <label class='col col-sm-4 col-form-label text-sm-right' for='text'>
                    {__('text')}
                </label>
                <div class='col-sm pl-sm-3 pr-sm-5 order-last order-sm-2'>
                    <textarea class='form-control' id='text' name='text' maxlength='1520'
                              required></textarea>
                </div>
            </div>
        </div>
    </fieldset>

    <input type='hidden' name='jtl_token' value='{$sessionToken}'/>

    <div class='save-wrapper'>
        <div class='row'>
            <div class='ml-auto col-sm-6 col-xl-auto submit'>
                <button type='submit' class='btn btn-primary btn-block'>
                    {ucfirst(__('send'))}
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    const smsInputs = [
        document.getElementById('wrapFlash'),
        document.getElementById('wrapPerformanceTracking'),
        document.getElementById('wrapDelay'),
        document.getElementById('wrapForeignId'),
        document.getElementById('wrapLabel'),
    ]
    const voiceInputs = []
    const text = document.getElementById('text')

    Array.from(document.querySelectorAll('input[name="msgType"]'))
        .forEach(t => t.addEventListener('change', e => {
            const type = e.target.value

            smsInputs.forEach(e => e.classList.toggle('d-none'))
            voiceInputs.forEach(e => e.classList.toggle('d-none'))

            text.setAttribute('maxlength', String('sms' === type ? 1520 : 10000))
        }))
</script>

import './component';
import './config';
import './preview';


Shopware.Service('cmsService').registerCmsElement({
	name: 'tocafix-job-teaser',
	label: 'tocafix.element.job-teaser.label',
	component: 'sw-cms-el-tocafix-job-teaser',
	configComponent: 'sw-cms-el-config-tocafix-job-teaser',
	previewComponent: 'sw-cms-el-preview-tocafix-job-teaser',

	defaultConfig: {
        job: {
            source: 'static',
            value: [],
            required: true,
            entity: {
                name: 'tocafix_job'
            }
        }
    }
});

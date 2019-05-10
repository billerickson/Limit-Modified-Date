import { registerPlugin } from '@wordpress/plugins';
import { default as LimitModifiedDate } from './components/limit-modified-date';

registerPlugin(
	'limit-modified-date',
	{
		render: LimitModifiedDate,
	}
);

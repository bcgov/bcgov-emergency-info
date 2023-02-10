import { admin_sample_function } from '../admin';
import { sample_function } from '../public';

describe('PluginName Site', () => {
	test('Testing admin ts', () => {
		const results = admin_sample_function('something');
		expect(results).not.toBeUndefined();
		expect(results).toBe('admin sample function result');
	});
	test('Testing app ts', () => {
		const results = sample_function('something');
		expect(results).not.toBeUndefined();
		expect(results).toBe('sample function result');
	});
});

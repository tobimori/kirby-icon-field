<?php

// Kirby magic methods for Mago, never loaded. Mago can take a stub as the class declaration, so the stub repeats the real class header

namespace Kirby\Form {
	use Kirby\Cms\HasSiblings;
	use Kirby\Toolkit\Component;

	/**
	 * Props, computed values and methods of the icon field and the tags field it extends
	 *
	 * @method array<string, mixed> props()
	 * @method string|false folder()
	 * @method string|null sprite()
	 * @method array<array-key, mixed> include()
	 * @method array<array-key, mixed> exclude()
	 * @method string separator()
	 * @method list<mixed> sanitizeOptions(array<array-key, mixed> $values)
	 */
	class Field extends Component
	{
		use HasSiblings;
		use Mixin\Api;
		use Mixin\Model;
		use Mixin\Translatable;
		use Mixin\Validation;
		use Mixin\When;
		use Mixin\Value {
			isEmptyValue as protected isEmptyValueFromMixin;
		}
	}
}

import 'package:pawlly/utils/library.dart';
class ProductCategoryScreenShimmer extends StatelessWidget {
  const ProductCategoryScreenShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return const ScreenShimmer(shimmerComponent: CategoryItemComponentsShimmer());
  }
}
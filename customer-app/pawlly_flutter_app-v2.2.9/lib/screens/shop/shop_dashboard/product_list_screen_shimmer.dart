import 'package:pawlly/utils/library.dart';
class ProductListScreenShimmer extends StatelessWidget {
  const ProductListScreenShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return const ScreenShimmer(shimmerComponent: FeaturedProductItemComponentShimmer());
  }
}
import 'package:pawlly/utils/library.dart';
class NewOrderScreenShimmer extends StatelessWidget {
  const NewOrderScreenShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return const ScreenShimmer(shimmerComponent: NewOrderCardShimmer());
  }
}
import 'package:pawlly/utils/library.dart';

class MyPetsScreenShimmer extends StatelessWidget {
  const MyPetsScreenShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return const ScreenShimmer(shimmerComponent: PetCardComponentShimmer());
  }
}
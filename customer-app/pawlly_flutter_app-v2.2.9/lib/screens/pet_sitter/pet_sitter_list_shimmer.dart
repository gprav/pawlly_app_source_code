import 'package:pawlly/utils/library.dart';

class PetSitterListShimmer extends StatelessWidget {
  const PetSitterListShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Wrap(
      runSpacing: 16,
      spacing: 16,
      alignment: WrapAlignment.center,
      children: List.generate(
        10,
        (index) => const PetSitterItemComponentShimmer(),
      ),
    );
  }
}
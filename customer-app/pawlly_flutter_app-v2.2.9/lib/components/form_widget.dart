// ignore_for_file: must_be_immutable

import 'package:pawlly/utils/library.dart';

class FormWidget extends StatelessWidget {
  String name;
  Widget child;

  FormWidget({
    super.key,
    required this.name,
    required this.child,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(name, style: boldTextStyle(size: 12)),
        8.height,
        child,
      ],
    );
  }
}
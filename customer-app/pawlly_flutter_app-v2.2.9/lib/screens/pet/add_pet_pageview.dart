import 'package:get/get.dart';
import 'package:pawlly/utils/library.dart';

class AddPetInfoScreen extends StatelessWidget {
  AddPetInfoScreen({super.key});

  final AddPetInfoController addPetInfoController = Get.put(AddPetInfoController());

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: true,
      onPopInvokedWithResult: (didPop, result) {
        if (didPop) return;

        if (addPetInfoController.currentPage.value == 0 || addPetInfoController.isUpdateProfile.value) {
          Get.back(); // Allow pop
        } else {
          addPetInfoController.handlePrevious(); // Navigate to previous step
        }
      },
      child: AppScaffold(
        isCenterTitle: true,
        isLoading: addPetInfoController.isLoading,
        appBarTitle: Obx(
          () => Text(
            addPetInfoController.isUpdateProfile.value ? locale.value.editPetInfo : addPetInfoController.pageViewElementList[addPetInfoController.currentPage.value].appBarTitle,
            style: primaryTextStyle(size: 18),
          ),
        ),
        body: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            16.height,
            Obx(
              () => StepsRow(
                isStep2Active: addPetInfoController.pageViewElementList[addPetInfoController.currentPage.value].isStep2Active,
                isStep3Active: addPetInfoController.pageViewElementList[addPetInfoController.currentPage.value].isStep3Active,
                midImg1: addPetInfoController.pageViewElementList[addPetInfoController.currentPage.value].midImg1,
                midImg2: addPetInfoController.pageViewElementList[addPetInfoController.currentPage.value].midImg2,
              ).paddingSymmetric(horizontal: 16),
            ),
            32.height,
            PageView.builder(
              itemCount: addPetInfoController.pageViewElementList.length,
              controller: addPetInfoController.pageController,
              onPageChanged: (int index) {
                addPetInfoController.currentPage(index);
              },
              physics: const NeverScrollableScrollPhysics(),
              itemBuilder: (context, index) {
                return AnimatedScrollView(
                  onSwipeRefresh: () async {
                    addPetInfoController.page(1);
                    return addPetInfoController.init();
                  },
                  padding: const EdgeInsets.only(bottom: 16),
                  onNextPage: () async {
                    if (!addPetInfoController.isLastPage.value) {
                      addPetInfoController.page(addPetInfoController.page.value + 1);
                      addPetInfoController.init();
                    }
                  },
                  children: [
                    Column(
                      mainAxisAlignment: MainAxisAlignment.start,
                      crossAxisAlignment: CrossAxisAlignment.center,
                      children: [
                        if (index == 0) ...[
                          Obx(
                            () => addPetInfoController.hasErrorFetchingbookingList.value
                                ? NoDataWidget(
                                    title: addPetInfoController.errorMessage.value,
                                    retryText: locale.value.reload,
                                    titleTextStyle: primaryTextStyle(),
                                    imageWidget: const ErrorStateWidget(),
                                    onRetry: () {
                                      addPetInfoController.getPetTypesApi();
                                    },
                                  ).paddingTop(Get.height * 0.15).paddingSymmetric(horizontal: 16)
                                : addPetInfoController.petTypeList.isEmpty && !addPetInfoController.isLoading.value
                                    ? NoDataWidget(
                                        title: locale.value.itAppearsTheAdmin,
                                        imageWidget: const EmptyStateWidget(),
                                        subTitle: locale.value.youCanUtilizeThe,
                                        retryText: locale.value.sendRequestToAdmin,
                                        onRetry: () {
                                          Get.back();
                                        },
                                      ).paddingSymmetric(horizontal: 16)
                                    : AddPetStep1Screen(),
                          )
                        ],
                        if (index == 1) ...[AddPetStep2Screen()],
                        if (index == 2) ...[AddPetStep3Screen()],
                      ],
                    ),
                  ],
                );
              },
            ).expand(),
          ],
        ),
      ),
    );
  }
}
